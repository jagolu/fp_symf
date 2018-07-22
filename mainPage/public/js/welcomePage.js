$(document).ready(function(){
    //Timeout for possible alerts
    setTimeout(function() {
        document.getElementById("alert").innerHTML = "";
    }, 3000);
    //Show formLogin
    $("#loginButton").click(function(){
        document.getElementById("buttons").style.visibility = "hidden";
        document.getElementById("buttons").style.display= "none";
        setTimeout(function(){
            $("#buttons").fadeOut(); 
        },0);
        setTimeout(function(){
            $("#loginDiv").fadeIn(400);
            document.getElementById("loginDiv").style.visibility = "visible";
            document.getElementById("loginDiv").style.display = "block";
        }, 450); 
    });
    //Show formCheckIn
    $("#checkInButton").click(function(){
        document.getElementById("buttons").style.visibility = "hidden";
        document.getElementById("buttons").style.display = "none";
        setTimeout(function(){
            $("#buttons").fadeOut(); 
        },0);
        setTimeout(function(){
            $("#checkInDiv").fadeIn(400);
            document.getElementById("checkInDiv").style.visibility = "visible";
            document.getElementById("checkInDiv").style.display = "block";
        }, 450); 
    });
    //Show rememberPassword
    $("#rememberPasswordButton").click(function(){
        document.getElementById("loginDiv").style.visibility = "hidden";
        document.getElementById("loginDiv").style.display= "none";
        setTimeout(function(){
            $("#loginDiv").fadeOut(); 
        },0);
        setTimeout(function(){
            $("#rememberPassword").fadeIn(400);
            document.getElementById("rememberPassword").style.visibility = "visible";
            document.getElementById("rememberPassword").style.display = "block";
        }, 450); 
    });
    //Come back buttons
    $("#logInDivBack").click(function(){
        document.getElementById("loginDiv").style.visibility = "hidden";
        document.getElementById("loginDiv").style.display= "none";
        setTimeout(function(){
            $("#loginDiv").fadeOut(); 
        },0);
        setTimeout(function(){
            $("#buttons").fadeIn(400);
            document.getElementById("buttons").style.visibility = "visible";
            document.getElementById("buttons").style.display = "block";
        }, 450); 
        document.getElementById("logInEmail").value="";
        document.getElementById("logInPassword").value="";
        itsWell('logInEmail');
        itsWell('logInPassword');
    });
    $("#checkInDivBack").click(function(){
        document.getElementById("checkInDiv").style.visibility = "hidden";
        document.getElementById("checkInDiv").style.display= "none";
        setTimeout(function(){
            $("#checkInDiv").fadeOut(); 
        },0);
        setTimeout(function(){
            $("#buttons").fadeIn(400);
            document.getElementById("buttons").style.visibility = "visible";
            document.getElementById("buttons").style.display = "block";
        }, 450); 
        document.getElementById("checkInEmail").value="";
        document.getElementById("checkInNickname").value="";
        document.getElementById("checkInPassword").value="";
        document.getElementById("checkInRepeatPassword").value="";
        itsWell('checkInEmail');
        itsWell('checkInNickname');
        itsWell('checkInPassword');
        itsWell('checkInRepeatPassword');
    });
    $("#rememberDivBack").click(function(){
        document.getElementById("rememberPassword").style.visibility = "hidden";
        document.getElementById("rememberPassword").style.display= "none";
        setTimeout(function(){
            $("#rememberPassword").fadeOut(); 
        },0);
        setTimeout(function(){
            $("#loginDiv").fadeIn(400);
            document.getElementById("loginDiv").style.visibility = "visible";
            document.getElementById("loginDiv").style.display = "block";
        }, 450); 
        document.getElementById("rememberPasswordEmail").value="";
        itsWell('rememberPasswordEmail');
    });
});
//Validate checkIn inputs
function validateCheckIn(){
    ready = true;
    var inputs = [];
    var messages = [];
    if(notRightEmail("checkInEmail")){
        inputs.push("checkInEmail");
        messages.push("- Email no valido");
        ready=false;
    }
    if(document.getElementById("checkInEmail").value.length<5 || document.getElementById("checkInEmail").value.length>50){
        if(inputs.length == 0){
            inputs.push("checkInEmail");
            messages.push("- Email no valido");
            ready=false;
        }
    }
    else itsWell("checkInEmail");
    if(document.getElementById('checkInNickname').value.length<3 || document.getElementById('checkInNickname').value.length>20){
        inputs.push("checkInNickname");
        messages.push("- El nickname debe tener entre 3 y 20 caracteres");
        ready=false;
    }
    else itsWell("checkInNickname");
    if(distinctPassword()){
        inputs.push("checkInPassword");
        messages.push("- Las contraseñas no coinciden");
        ready=false;
    }
    if(document.getElementById('checkInPassword').value.length<8 || document.getElementById('checkInPassword').value.length>20){
        inputs.push("checkInPassword");
        messages.push("- La contraseña debe estar entre 8 y 20 caracteres");
        ready=false;
    }
    else itsWell("checkInPassword");
    if(rightChar("checkInPassword")){
        inputs.push("checkInPassword");
        messages.push("- La contraseña solo permite letras, numeros, guiones, puntos, barras bajas y espacios");
        ready=false;
    }
    if(rightChar("checkInNickname")){
        inputs.push("checkInNickname");
        messages.push("- El nickname solo permite letras, numeros, guiones, puntos, barras bajas y espacios");
        ready=false;
    }
    if(!ready) itsWrong(inputs, messages);
    return ready;
}
//Validate logIn inputs
function validateLogIn(){
    ready = true;
    var inputs = [];
    var messages = [];
    if(notRightEmail("logInEmail")){
        inputs.push("logInEmail");
        messages.push("- Email no valido");
        ready=false;
    }
    if(document.getElementById("logInEmail").value.length<5 || document.getElementById("logInEmail").value.length>50){
        if(inputs.length == 0){
            inputs.push("logInEmail");
            messages.push("- Email no valido");
            ready=false;
        }
    }
    else itsWell("logInEmail");
    if(document.getElementById('logInPassword').value.length<8 || document.getElementById('logInPassword').value.length>20){
        inputs.push("logInPassword");
        messages.push("- La contraseña debe estar entre 8 y 20 caracteres");
        ready=false;
    }
    else itsWell("logInPassword");
    if(rightChar("logInPassword")){
        inputs.push("checkInPassword");
        messages.push("- Requerda que la contraseña solo permite letras, numeros, guiones, puntos, barras bajas y espacios");
        ready=false;
    }
    if(!ready) itsWrong(inputs, messages);
    return ready;
}
//Validate remember
function validateRemember(){
    ready = true;
    var inputs = [];
    var messages = [];
    if(notRightEmail("rememberPasswordEmail")){
        inputs.push("rememberPasswordEmail");
        messages.push("- Email no valido");
        ready=false;
    }
    if(document.getElementById("rememberPasswordEmail").value.length<5 || document.getElementById("rememberPasswordEmail").value.length>50){
        if(inputs.length == 0){
            inputs.push("rememberPasswordEmail");
            messages.push("- Email no valido");
            ready=false;
        }
    }
    if(!ready) itsWrong(inputs, messages);
    return ready;
}
function notRightEmail(id){
    campo = document.getElementById(id).value;
    if(campo.length<5) return false;
    emailRegex =/^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
    if (emailRegex.test(campo)) return false;
    else return true;
}
function itsWrong(id, message){
    messages = "";
    for(i=0; i<id.length; i++){
        document.getElementById(id[i]).style.backgroundColor="#8B0000";
        document.getElementById(id[i]).value="";
        messages = messages.concat("</br>");
        messages = messages.concat(message[i]);
    }
    document.getElementById("checkInRepeatPassword").value="";
    document.getElementById("checkInPassword").value="";
    document.getElementById("logInPassword").value="";
    alert = "<div class=\"alert alert-danger\" role=\"alert\">";
    alert = alert.concat(messages);
    alert = alert.concat("</br></br></div>");
    document.getElementById("alert").innerHTML = alert;
    setTimeout(function() {document.getElementById("alert").innerHTML = "";}, 3000);
}
function itsWell(id){
    document.getElementById(id).style.backgroundColor="#343a40";
}
function distinctPassword(){
    pass1 = document.getElementById('checkInPassword').value;
    pass2 = document.getElementById('checkInRepeatPassword').value;
    if(pass1 != pass2) return true;
    else return false;
}
function rightChar(id){
    pattern =/[^\w.]+/;
    password = document.getElementById(id).value;
    if(pattern.test(password)) return true;
    else return false;
}