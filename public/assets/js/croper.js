// Start upload preview image
$(".gambar").attr("src", "../public/assets/images/up_img.jpg");
						var $uploadCrop,
						tempFilename,
						rawImg,
						imageId;
						function readFile(input) {
				 			if (input.files && input.files[0]) {
				              var reader = new FileReader();
					            reader.onload = function (e) {
									$('.upload-demo').addClass('ready');
									$('#cropImagePop').modal('show');
						            rawImg = e.target.result;
					            }
					            reader.readAsDataURL(input.files[0]);
					        }
					        else {
						    //    swal("Sorry - you're browser doesn't support the FileReader API");
						    }
						}

						$uploadCrop = $('#upload-demo').croppie({
							viewport: {
								width: 350,
								height: 332,
							},
							enableResize: false,
							enforceBoundary: false,
							enableZoom: true,
							enableExif: true
						});
						$('#cropImagePop').on('shown.bs.modal', function(){
							// alert('Shown pop');
							$uploadCrop.croppie('bind', {
				        		url: rawImg
				        	}).then(function(){
				        		console.log('jQuery bind complete');
				        	});
						});

						$('.item-img').on('change', function () { imageId = $(this).data('id'); tempFilename = $(this).val();
																										 $('#cancelCropBtn').data('id', imageId); readFile(this); });
						$('#cropImageBtn').on('click', function (ev) {
							$uploadCrop.croppie('result', {
								type: 'base64',
								format: 'jpeg',
								size: {width: 580, height: 550}
							}).then(function (resp)
							{
								$('#item-img-output').attr('src', resp);
								$("#img1").val(resp);
								$('#cropImagePop').modal('hide');
							});
						   $uploadCrop.croppie('result', {
								type: 'base64',
								format: 'jpeg',
								size:  {width: 320, height: 210}
							}).then(function (resp)
							{
									$('#item-img-output2').attr('src', resp);
								    $("#img2").val(resp);
								  //$('#cropImagePop').modal('hide');
							});

						});
				// End upload preview image