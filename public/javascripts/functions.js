function slide(el) {
  var slide = new Fx.Slide(el, {
    duration: 'short'
  }).hide();
  el.store('slide', slide);
}
