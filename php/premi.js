/*
* Funzione per mostrare la descrizione relativa al premio,
* nel caso l'utente si soffermi sopra col mouse.
* Quando il mouse verrà spostato, la descrizione tornerà nell'ombra.
*/

function show(count){
    description = "desc" + count;
    title = "tit" + count;
    point = "punti" + count;
    document.getElementById(description).style.display = 'block';
    document.getElementById(title).style.display = 'none';
    document.getElementById(point).style.display = 'none';
}

function hide(count){
  description = "desc" + count;
  title = "tit" + count;
  document.getElementById(description).style.display = 'none';
  document.getElementById(title).style.display = 'block';
  document.getElementById(point).style.display = 'block';
}

/*
* Funzione per visualizzare la pagina in Dettagli oppure con Icone
*/

function weep(status) {
  if(status == 0){
    try{
      document.getElementById('premi').style.display = 'none';
      document.getElementById('premiNoiosi').style.display = 'block';
    }catch(error){
      console.log("nessun premio");
    }
  }else if(status == 1){
    try{
      document.getElementById('premiNoiosi').style.display = 'none';
      document.getElementById('premi').style.display = 'block';
    }catch(error){
      console.log("nessun premio");
    }
  }
}
