<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>

<div id='div_getTokenAlert' style="display: none;"></div>

<form class="form-horizontal">
	<fieldset>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Login API Tesla}}</label>
			<div class="col-sm-2">
				<input class="configKey form-control" data-l1key="email"
					placeholder="Email de votre compte sur le site Tesla" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Mot de passe API Tesla}}</label>
			<div class="col-sm-2">
				<input class="configKey form-control" data-l1key="password"
					placeholder="Mot de passe de votre compte sur le site Tesla" />
			</div>
		</div>
	</fieldset>
</form>

<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
<button type="button" class="btn btn-primary" id="getToken">xxx</button>

<div class="form-group">
			<div class="col-sm-8">
				<a class="btn btn-success" id="bt_getToken"><i class='fa fa-refresh'></i> {{(re)Creer token}}</a>
			</div>
		</div>

<script>
	//console.log("token" + $( "[id=token]").val());

	$('#bt_getToken').on('click',function(){
        //console.log($(this).closest('.email').attr('data-l1key'));
    	//regenerateToken($(this).closest('.email').attr('data-l1key'), $(this).closest('.password').attr('data-l1key'));
    	
	getToken($( "input[data-l1key=email]").value(), $( "input[data-l1key=password]").value());
    	
    });

    function getToken(email, password){
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/tesla/core/ajax/tesla.ajax.php", // url du fichier php
            data: {
                action: "createToken",
                email: email,
                password: password
            },
            dataType: 'json',
            error: function (request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{Creation de Token réussie}} ', level: 'success'});
            //document.getElementById("token").value=data.result;
            $( "[id=token]").val(value=data.result);
        }
    });
    };
</script>