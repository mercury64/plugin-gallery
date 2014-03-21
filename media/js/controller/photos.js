cms.init.add('photos_index', function(){
	$('#photo-upload-button').click(function() {
		$('#photo-upload-form').click();
	});
	
	$(function() {
		cms.uploader.options.acceptedFiles = '.jpg,.jpeg,.png';
		cms.uploader.options.params = {id: CATEGORY_ID};
		cms.uploader.on('success', function(file, response) {
			response = $.parseJSON(response);
			if(response.code == 200) {
				var row = $(response.response).hide();
				$('.photos').append(row.fadeIn(500));
			}
		});

		$('.photos').on('click', '.icon-trash', function() {
			var cont = $(this).parent();
			var id = cont.data('id');

			Api.post(SITE_URL + 'api-photos.delete', {id: id}, function(resp){
				if(resp.response) {
					cont.fadeOut(function() {
						$(this).remove();
					});
				}
			},'json');
		});
	});
	
	$('.photos').on('click', '.icon-picture', function() {
		var cont = $(this).parent();
		var id = cont.data('id');
		var self = $(this);
		
		Api.post(SITE_URL + 'api-photos.category_image', {id: id, category_id: CATEGORY_ID}, function(request){
			if(request.response) {
				$('.photos .thumbnail').each(function() {
					if($(this).hasClass('category-image')) {
						$(this)
							.removeClass('category-image');

						$('<i class="icon-picture option"></i>').insertBefore($(this));
					}
				});
	
				$('.thumbnail', cont).addClass('category-image');
				self.remove();
			}
		},'json');
	});

	$('.categories').on('click', '.icon-trash', function() {
		if ( ! confirm(__('Are you sure?')))
			return;
		
		var cont = $(this).parent();
		var id = cont.data('id');
		
		Api.post(SITE_URL + 'api-photos.category_delete', {id: id}, function(request){
			if(request.response) {
				window.location = '';
			}
		},'json');
	});

	$( ".droppable .span1" ).droppable({
//		tolerance: 'intersect',
		accept: ".sortable .span1",
		hoverClass: "drop",
		drop: function( event, ui ) {
			var element = $(ui.draggable);
			var id = element.data('id');
			var category_id = $(this).data('id');
			
			element.hide();
			Api.post(SITE_URL + 'api-photos.move', {id: id, category_id: category_id, category_image: $('.thumbnail ', element).hasClass('category-image')}, function(request){
				if(request.response) {
					cms.loader.hide();
					element.remove();
				} else {
					element.show();
				}
			},'json');
		},
    });
	
	$('.categories').sortable({
		cursor: 'move',
		items: '.ui-sort',
		update: function(event, ui){
			var pos = $('.categories').sortable("toArray", {attribute: 'data-id'});
			cms.loader.show();
			Api.post(SITE_URL + 'api-photos.categories_sort', {pos: pos, parent_id: CATEGORY_ID}, function(request){
				if(request.response) {
					cms.loader.hide();
				}
			},'json');
		}
	});

	$('.sortable').sortable({
		cursor: 'move',
		update: function(event, ui){
			var pos = $('.sortable').sortable("toArray", {attribute: 'data-id'});
			cms.loader.show();
			Api.post(SITE_URL + 'api-photos.sort', {pos: pos, category_id: CATEGORY_ID}, function(request){
				if(request.response) {
					cms.loader.hide();
				}
			},'json');
		}
	});

	$('#create-category').click(function() {
		$('#category-modal')
			.on('submit', 'form', function(e) {
				save_category_modal_form($(this));
			
				e.preventDefault();
			})
            .modal();
	});
	
	$('#edit-category').click(function(){
		Api.get(SITE_URL + 'api-photos.category_edit', {
			id: CATEGORY_ID
		}, function(resp) {
			if(resp.response) {
				$(resp.response)
					.on('submit', 'form', function(e) {
						save_category_modal_form($(this));
						
						
						e.preventDefault();
					})
					.modal();
			}
		}, 'html');
	});
	
	$('#upload-video form').on('submit', function(e){
		Api.post($(this).attr('action'), $(this).serialize(), function(response) {
			var row = $(response.response).hide();
			$('.photos').append(row.fadeIn(500));
		}, 'json');
		
		e.preventDefault();
	});
});

function save_category_modal_form(form) {
	var id = form.find('input[name="id"]').val();
	var title = form.find('input[name="title"]').val();
	var description = form.find('textarea[name="description"]').val();
	var slug = form.find('input[name="slug"]').val();

	Api.post(SITE_URL + 'api-photos.category_save', {
		id: id,
		title: title,
		description: description,
		slug: slug,
		parent_id: CATEGORY_ID
	}, function(resp) {
		if(resp.response) {
			window.location = '';
		}
	}, 'json');
}
