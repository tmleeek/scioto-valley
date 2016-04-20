solide(document).ready(function() {
	var navshow = solide('#nav_show').val();
	if(navshow == 'no'){
		solide('#nav_show').closest('tr').next('tr').next('tr').next('tr').hide();
	}
	
	var paginationshow = solide('#pagination_show').val();
	if(paginationshow == 'no'){
		solide('#pagination_show').closest('tr').next('tr').next('tr').next('tr').hide();
	}
	
	var loadershow = solide('#loader_show').val();
	if(loadershow == 0){
		solide('#loader_show').closest('tr').next('tr').next('tr').hide();
		solide('#loader_show').closest('tr').next('tr').next('tr').next('tr').hide();
	}

	var type = solide('#type').val();
	if(type != 'overlay'){
		solide('#type').closest('tr').next('tr').next('tr').hide();
		solide('#type').closest('tr').next('tr').next('tr').next('tr').hide();
		solide('#type').closest('tr').next('tr').next('tr').next('tr').next('tr').hide();
	}

});