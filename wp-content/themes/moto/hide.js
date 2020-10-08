String.prototype.printAddr =
function (_hamper,_prefix,_postfix,_face)
{
  _hamper=
  _prefix+
  "@"+
  this+
  (_postfix || '')
  document.write((_face||_hamper).link("mailto:"+_hamper));
}
