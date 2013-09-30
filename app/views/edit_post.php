<form action="api/edit_post" method="post">
  <article id="post-form">
    <?php $this->partial('post_form', array(
      'content' => $post->content,
      'form_class' => "user_{$post->user_id}"
    )); ?>
  </article>
</form>
