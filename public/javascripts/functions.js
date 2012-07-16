function slide(el) {
  var slide = new Fx.Slide(el, {
    duration: 'short'
  }).hide();
  el.store('slide', slide);
}

function upload_complete(filename, original, type) {
  var obj = $('upload_file').retrieve('object');
  obj.showDetails(filename, original, type);
}
