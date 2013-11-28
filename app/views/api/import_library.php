<?php

require_once GRID_DIR . '/library/simplehtmldom/simple_html_dom.php';

if (!empty($_SESSION['is_admin']) && !empty($_POST['urls'])) {
  $results = array();
  $urls = explode("\n", $_POST['urls']);
  foreach ($urls as $url) {
    $url = trim($url);
    if (empty($url) || substr($url, 0, 4) != 'http') {
      continue;
    }
    if (substr($url, -4, 4) == '.pdf') {
      download_pdf($url);
    } else {
      import_article($url);
    }
  }
}
$this->redirect('/c/library');
exit;

function import_article($url) {
  $md5 = md5($url);
  $path = GRID_DIR . "/import/$md5.html";
  $token = READABILITY_API_KEY;
  $url_encoded = urlencode($url);
  $request = "https://readability.com/api/content/v1/parser?url=$url_encoded&token=$token";
  $json = download_file($request);
  $result = json_decode($json);
  if (!empty($result->title)) {
    $content = process_content($result);
    if (!empty($content)) {
      file_put_contents($path, $content);
      return $path;
    }
  }
  return null;
}

function download_pdf($url) {
  $filename = basename($url);
  $path = GRID_DIR . "/import/$filename";
  $data = download_file($url);
  if (!empty($data)) {
    file_put_contents(GRID_DIR . "/import/$filename", $data);
    return $path;
  }
  return null;
}

function process_content($article) {
  $image_types = array(
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'png' => 'image/png',
    'bmp' => 'image/bmp'
  );
  if (!empty($article->content)) {
    $md5 = md5($article->url);
    $article->md5 = $md5;
    $json = json_encode($article);
    $domain = str_replace('www.', '', $article->domain);
    $domain = "<a href=\"{$article->url}\">{$domain}</a>";
    $meta = ($article->author) ? "By $article->author / $domain" : $domain;
    $content = <<<END
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>{$article->title}</title>
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <style>
    
    body {
      text-align: center;
      margin: 0;
    }
    
    #article-$md5 {
      font: 16px/24px serif;
      width: 100%;
      text-align: left;
      margin: 0 auto;
      padding-bottom: 25px;
    }
    
    #article-$md5 a {
      color: #03C;
    }
    
    #article-$md5 img {
      display: block;
      max-width: 100%;
      width: auto;
      height: auto;
    }
    
    #article-$md5 > h1 {
      font: bold 24px/24px helvetica neue, helvetica, sans-serif;
      margin: 25px 0 10px 0;
    }
    
    #article-$md5 > .meta {
      color: #666;
      font: 11px verdana, sans-serif;
      text-transform: uppercase;
      border-top: 1px solid #ccc;
      padding-top: 10px;
    }
    
    #article-$md5 > .meta a {
      color: #666;
    }
    
    #article-$md5 > h1,
    #article-$md5 > .meta,
    #article-$md5 p {
      padding-left: 15px;
      padding-right: 15px;
    }
    
    @media
      only screen and (min-width: 580px) {
      
      #article-$md5 {
        width: 550px;
        padding-bottom: 50px;
      }
      
      #article-$md5 > h1 {
        font: bold 30px/30px helvetica neue, helvetica, sans-serif;
        margin: 50px 0 10px 0;
      }
      
      #article-$md5 {
        font: 16px/24px serif;
      }
      
      #article-$md5 > h1,
      #article-$md5 > .meta,
      #article-$md5 p {
        padding-left: 0;
        padding-right: 0;
      }
      
    }
    
    </style>
  </head>
  <body>
    <!-- occupy.here meta start -->
    <script id="meta">
    var meta = $json;
    </script>
    <!-- occupy.here meta end -->
    <!-- occupy.here article start -->
    <div id="article-$md5">
      <h1>$article->title</h1>
      <div class="meta">
        $meta
      </div>
      <div class="content">
        $article->content
      </div>
    </div>
    <!-- occupy.here article end -->
  </body>
</html>
END;
    $html = new simple_html_dom();
    $html->load($content, true, false);
    $images = array();
    foreach ($html->find('img') as $img) {
      $images[] = urldecode($img->src);
    }
    $image_data = array();
    foreach ($images as $url) {
      if (!preg_match('/\.(\w+)$/', $url, $matches)) {
        continue;
      }
      list(, $ext) = $matches;
      $ext = strtolower($ext);
      if (empty($image_types[$ext])) {
        continue;
      }
      $type = $image_types[$ext];
      $data = download_file($url);
      $tmp = tempnam(GRID_DIR . "/public/uploads/tmp", basename($url));
      file_put_contents($tmp, $data);
      list($width, $height) = getimagesize($tmp);
      unlink($tmp);
      $base64 = base64_encode($data);
      $data_url = "data:$type;base64,$base64";
      if (!empty($data)) {
        $img_list = $html->find("img[src=$url]");
        foreach ($img_list as $img) {
          $img->src = $data_url;
          $img->width = $width;
          $img->height = $height;
        }
      }
    }
    return $html->save();
  }
}

function download_file($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  $data = curl_exec($ch);
  return $data;
}

?>
