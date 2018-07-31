$(document).ready(function(){
    //Timeout for possible alerts
    setTimeout(function() {
        document.getElementById("alert").innerHTML = "";
    }, 3000);
    $('#leagueInput').on('change', function(){
        if (this.checked) {
            document.getElementById('leagueLabel').className="col-md-6  btn btn-success active";
        } else {
            document.getElementById('leagueLabel').className="col-md-6  btn btn-success";
        }
    });
    //When champions and cup mode it will be able
    /*$('#championsInput').on('change', function(){
        if (this.checked) {
            document.getElementById('championsLabel').className="col-md-6  btn btn-success active";
        } else {
            document.getElementById('championsLabel').className="col-md-6  btn btn-success";
        }
    });
    $('#cupInput').on('change', function(){
        if (this.checked) {
            document.getElementById('cupLabel').className="col-md-6  btn btn-success active";
        } else {
            document.getElementById('cupLabel').className="col-md-6  btn btn-success";
        }
    });*/
});
function validateCreateRoom(){
    ready = true;
    var inputs = [];
    var messages = [];
    if(document.getElementById('nameRoom').value.length<3 || document.getElementById('nameRoom').value.length>20){
        inputs.push("nameRoom");
        messages.push("- El nombre de sala debe tener entre 3 y 20 caracteres");
        ready=false;
    }
    else itsWell("nameRoom");
    if(distinctPassword()){
        inputs.push("password");
        messages.push("- Las contraseñas no coinciden");
        ready=false;
    }
    if(document.getElementById('password').value.length<8 || document.getElementById('checkInPassword').value.length>20){
        inputs.push("password");
        messages.push("- La contraseña debe estar entre 8 y 20 caracteres");
        ready=false;
    }
    else itsWell("password");
    if(rightChar("password")){
        inputs.push("password");
        messages.push("- La contraseña solo permite letras, numeros, guiones, puntos, barras bajas y espacios");
        ready=false;
    }
    if(rightChar("nameRoom")){
        inputs.push("nameRoom");
        messages.push("- El nombre de sala solo permite letras, numeros, guiones, puntos, barras bajas y espacios");
        ready=false;
    }
    if(!ready) itsWrong(inputs, messages);
    return ready;
}
function distinctPassword(){
    pass1 = document.getElementById('password').value;
    pass2 = document.getElementById('repeatPassword').value;
    if(pass1 != pass2) return true;
    else return false;
}
function itsWrong(id, message){
    messages = "";
    for(i=0; i<id.length; i++){
        document.getElementById(id[i]).style.backgroundColor="#8B0000";
        document.getElementById(id[i]).value="";
        messages = messages.concat("</br>");
        messages = messages.concat(message[i]);
    }
    document.getElementById("repeatPassword").value="";
    document.getElementById("password").value="";
    alert = "<div class=\"alert alert-danger\" role=\"alert\">";
    alert = alert.concat(messages);
    alert = alert.concat("</br></br></div>");
    document.getElementById("alert").innerHTML = alert;
    setTimeout(function() {document.getElementById("alert").innerHTML = "";}, 3000);
}
function itsWell(id){
    document.getElementById(id).style.backgroundColor="#343a40";
}
function rightChar(id){
    pattern =/[^\w.]+/;
    password = document.getElementById(id).value;
    if(pattern.test(password)) return true;
    else return false;
}