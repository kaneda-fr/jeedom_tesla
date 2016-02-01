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
			<div class="col-sm-4">
				<input class="configKey form-control" data-l1key="email"
					placeholder="Email de votre compte sur le site Tesla" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Mot de passe API Tesla}}</label>
			<div class="col-sm-4">
				<input class="configKey form-control" data-l1key="password"
					placeholder="Mot de passe de votre compte sur le site Tesla" />
			</div>
		</div>
		<center>
			<a class="btn btn-success" id="bt_getToken"><i class='fa fa-car'></i> {{Connection}}</a>
			<a class="btn btn-warning" id="bt_getTokenCancel"><i class='fa fa-hand-paper-o'></i> {{Annuler}}</a>
		</center>
	</fieldset>
</form>

<script>
$('#bt_getToken').on('click',function(){
	getToken($( "input[data-l1key=email]").value(), $( "input[data-l1key=password]").value());
	$('#md_modal').dialog('close');
});


$('#bt_getTokenCancel').on('click',function(){
	$('#md_modal').dialog('close');
});


</script>
