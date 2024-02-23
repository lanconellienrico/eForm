/*Nel caso non ci siano inviti a cui rispondere, viene nascosto il Submit */

window.onload = function(){
  var none = document.getElementById('none').innerHTML;
  if(none === '1'){
    document.getElementById('submit').style.display = "none";
  }else{
    document.getElementById('submit').style.display = "inline-block";
  }
};
