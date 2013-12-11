function loadSkinPreview(id)
{
	$('#skin_preview_'+id).html('<img src="datapack/skin/fighter/'+$('#skin_'+id).val()+'/front.png" width="80" height="80" alt="Front" style="float:left" /><img src="datapack/skin/fighter/'+$('#skin_'+id).val()+'/back.png" width="80" height="80" alt="Back" style="float:left" /><img src="datapack/skin/fighter/'+$('#skin_'+id).val()+'/trainer.png" width="48" height="96" alt="Trainer" style="float:left" /><br style="clear:both" />');
}
