/* funzione per mostrare il campo testuale da riempire
*  nel caso la domanda sia aperta, altrimenti la domanda
*  è chiusa e verranno mostrate le determinate opzioni
*/
window.onload = function() {
  /*se si procede con l'inserimento della domanda e si torna indietro
  * il SUBMIT del form solleva problemi di Reinvio Modulo, ricaricando
  * la pagina, se ne torna tranquillo e si ha una normale e corretta visualizzazione
  */
    /*if(!window.location.hash) {
        window.location = window.location + '#loaded';
        window.location.reload();
    }*/



  var tipo = document.getElementById('openClosed').innerHTML;
  if(tipo==='aperta'){
    document.getElementById('domandaAperta').style.display = 'block';
    document.getElementById('domandaChiusa').style.display = 'none';
  }else if(tipo==='chiusa'){
    document.getElementById('domandaChiusa').style.display = 'block';
    document.getElementById('domandaAperta').style.display = 'none';
  }


}
