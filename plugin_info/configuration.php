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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}

$apiAccessOK = false;
try {
	// Test access API
	$apiAccessOK = tesla::checkAPI();
	
} catch (Exception $e) {
}

?>
<form class="form-horizontal">
    <fieldset>
    	<legend><i class="fa fa-list-alt"></i> {{Etat}}</legend>
<?php    	
	echo '<div class="form-group">';
	echo '<label class="col-sm-4 control-label">{{API Tesla}}</label>';
	if (!$apiAccessOK) {
		echo '<div class="col-sm-1"><span class="label label-danger tooltips" style="font-size : 1em;" title="{{Verifiez vos information d\'identification Tesla & generez un token}}">NOK</span></div>';
	} else {
		echo '<div class="col-sm-1"><span class="label label-success" style="font-size : 1em;" title="{{'. $apiAccessOK . ' voiture(s)}}">OK</span></div>';
	}
	echo '</div>';
?>
  </fieldset>
</form>
<form class="form-horizontal">
	<fieldset>
		<legend><i class="fa fa-list-alt"></i> {{Authentification tesla}}</legend>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Login API Tesla}}</label>
			<div class="col-sm-2">
				<input class="configKey form-control" data-l1key="email" placeholder="Email de votre compte sur le site Tesla" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Mot de passe API Tesla}}</label>
			<div class="col-sm-2">
				<input class="configKey form-control" data-l1key="password" placeholder="Mot de passe de votre compte sur le site Tesla" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Token}}</label>
			<div class="col-sm-4">	
				<input id="token" class="configKey form-control" data-l1key="token" placeholder="Token pour acces a l'API" />
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-8">
				<a class="btn btn-success" id="bt_regenerateToken"><i class='fa fa-refresh'></i> {{(re)Creer token}}</a>
			</div>
		</div>
	</fieldset>
</form>

<script>
    $('#bt_regenerateToken').on('click',function(){
        //console.log($(this).closest('.email').attr('data-l1key'));
    	//regenerateToken($(this).closest('.email').attr('data-l1key'), $(this).closest('.password').attr('data-l1key'));
    	
    	//regenerateToken($( "input[data-l1key=email]").value(), $( "input[data-l1key=password]").value());

        $('#md_modal').dialog({title: "{{Connection au portail Tesla}}"});
        $('#md_modal').load('index.php?v=d&plugin=tesla&modal=get.token').dialog('open');
    	
    });

    function regenerateToken(email, password){
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

