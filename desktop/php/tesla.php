<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'tesla');
$eqLogics = eqLogic::byType('tesla');
?>

<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default btn-sm tooltips" id="bt_syncEqLogic" title="Snchroniser la liste des vehicules" style="width : 100%;margin-top : 5px;margin-bottom: 5px;">
                    <i class="fa fa-refresh"></i> {{Synchroniser}} <!-- changer pour votre type d'équipement -->
                </a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
            </ul>
            <ul id="ul_eqLogicView" class="nav nav-pills nav-stacked"></ul> <!-- la sidebar -->
        </div>
    </div>

    <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Liste des Vehicules}}</legend> <!-- changer pour votre type d'équipement -->

        <div class="eqLogicThumbnailContainer"></div> <!-- le container -->
    </div>

    <!-- Affichage de l'eqLogic sélectionné -->
    <div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <form class="form-horizontal">
            <fieldset>
                <legend>
                    <!-- Retour au Général et affichage de la configuration avancée -->
                    <i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}  
                    <i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i>
                </legend>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{Nom de la voiture}}</label>
                    <div class="col-sm-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" disabled placeholder="{{Nom de la voiture}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" >{{Objet parent}}</label>
                    <div class="col-sm-3">
                        <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                            <option value="">{{Aucun}}</option>
                            <?php
                            foreach (object::all() as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" >{{Activer}}</label>
                    <div class="col-sm-1">
                        <input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" size="16" checked/>
                    </div>
                    <label class="col-sm-1 control-label" >{{Visible}}</label>
                    <div class="col-sm-1">
                        <input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{VIN}}</label>
                    <div class="col-sm-3">
                        <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="vin" disabled placeholder="VIN Number"/>
                    </div>
                </div>
               	<div class="form-group">   
                    <label class="col-sm-3 control-label">{{Status}}</label>
                    <div class="col-sm-3">
                        <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="state" disabled placeholder="State"/>
                    </div>
                </div>
            </fieldset>
        </form>

        <legend>{{Tesla Info}}</legend>
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>{{Nom}}</th><th>{{Type}}</th><th>{{Action}}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<?php include_file('desktop', 'tesla', 'js', 'tesla'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
<?php include_file('core', 'plugin.ajax', 'js'); ?>