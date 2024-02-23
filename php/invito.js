/*
* Si vuole mostrare il titolo del Sondaggio mentre si seleziona il codice,
* dall'apposito input type='number'.
* Se il numero inserito in input corrisponde a quello di un sondaggio esistente,
* allora ne comparirà il titolo.
* Quando il numero in input cambia, il vecchio titolo torna nell'ombra e si ripete la procedura.
*/

window.onload = function() {

  var number = document.getElementById("sondaggio");
  var old = 0;
  var n = 0;
  number.addEventListener("input", ()=> {
    n = number.value;
    if(old!=0 && old!=n){
      try{
      document.getElementById(old).style.display = "none";
    }catch(Exception){};
    }
    try{
      document.getElementById(n).style.display = "inline";
    }catch(Exception){} finally{
      old = n;
    }
  });
};
