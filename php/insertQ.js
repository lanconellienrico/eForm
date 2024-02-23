/*
* Se si sta inserendo una Domanda Aperta, si mostra anche il campo Max-Max_Caratteri
* altrimenti, se si procede con l'inserire una domanda chiusa, la voce non sarà più visibile.
*/

function show(){
  document.getElementById('maxChar').style.visibility = "visible";
  document.getElementById('opzione').style.display = 'none';
}
function shut(){
  document.getElementById('maxChar').style.visibility = "hidden";
  document.getElementById('opzione').style.display = 'block';
}



window.onload = function() {

  //per poter aggiungere le opzioni alla domanda chiusa
  var elder = 0;
  var m = 0;
  var isDisplayed = new Array(5).fill(false);
  var nClosed = document.getElementById("nClosed");
  nClosed.addEventListener("input", ()=>{
    m = nClosed.value;
    if(elder < m){
      for(var i = elder; i < m; i++){
        if(!isDisplayed[i]){
          var mm = i;
          mm++;

          //creo lo spazio
          var space = document.createElement('br');
          var spaceName = "space" + mm;
          space.setAttribute('id', spaceName);
          document.getElementById('opzione').appendChild(space);

          //label dedicato con numerazione
          var labelName = "label" + mm;
          const numberOption = document.createElement('label');
          numberOption.setAttribute('for', optionName);
          numberOption.setAttribute('id', labelName);
          const labelText = document.createTextNode(mm + ") ");
          numberOption.appendChild(labelText);
          document.getElementById('opzione').appendChild(numberOption);

          //creazione dell'input text di ciascun opzione
          var optionName = "option" + mm;
          const option = document.createElement('input');
          option.setAttribute('class', 'options');
          option.setAttribute('name', optionName);
          option.setAttribute('id', optionName);
          option.setAttribute('type', 'text');
          option.setAttribute('maxlength', '64');
          option.setAttribute('required', 'required');
          document.getElementById('opzione').appendChild(option);

        }
      }
    }else if(elder > m){
      //nel caso il nuovo numero immesso sia inferiore a quello precedente
      //significa che il numero di opzioni è diminuito e l'ultimo input text dev'essere cancellato
      for(var i = elder; i > m; i--){
        var opzione = document.getElementById('opzione');
        var toRemove = document.getElementById('option'+elder);
        var labelToRemove = document.getElementById('label'+elder);
        var spaceToRemove = document.getElementById('space'+elder);
        opzione.removeChild(toRemove);
        opzione.removeChild(labelToRemove);
        opzione.removeChild(spaceToRemove);
      }
    }
    elder = m;
  })

};
