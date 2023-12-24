let oc3daigImage = {  
  oc3daigNumberImages: null,
  oc3daigImageSaveBtn:null,
  oc3daigImageGenerateBtn:null,
  oc3daigImageConvertBar:null,
  oc3daigImageLoading:null,
  oc3daigImageGrid:null,
  /*oc3daigImageSelectAll:null,*/
  oc3daigStartTime:null,
  oc3daigImageMessage:null,
  oc3daigImageGenerated:null,
  init: function(){
      that = this;
      oc3daigImageForm = document.querySelector("#oc3daig_img_gen_form");
      this.oc3daigImageGenerated = oc3daigImageForm.getElementsByClassName('image-generated')[0];
            this.oc3daigImageGrid = oc3daigImageForm.getElementsByClassName('image-grid')[0];
            this.oc3daigImageLoading = oc3daigImageForm.getElementsByClassName('image-generate-loading')[0];
            this.oc3daigImageSaveBtn = oc3daigImageForm.getElementsByClassName('image-generator-save')[0];
            this.oc3daigImageMessage = oc3daigImageForm.getElementsByClassName('oc3daig_message')[0];
            this.oc3daigImageConvertBar = oc3daigImageForm.getElementsByClassName('oc3daig-convert-bar')[0];
            this.oc3daigNumberImages = oc3daigImageForm.querySelector('select[name=oc3daig_images_count]');
            this.oc3daigImageGenerateBtn = oc3daigImageForm.querySelector('#oc3daig_submit');

            
        oc3daigImageForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var form_action = oc3daigImageForm.querySelectorAll('input[name=action]')[0].value;
                
                var num_images = parseInt(that.oc3daigNumberImages.value);
                if (num_images > 0) {
                        let imgform = new FormData(oc3daigImageForm);
                        let urlpars = new URLSearchParams(imgform);
                        let queryString = urlpars.toString();
                        that.oc3daigImageSaveBtn.style.display = 'none';
                        oc3daigImageLoadingEffect(that.oc3daigImageGenerateBtn);
                        that.oc3daigImageConvertBar.style.display = 'none';
                        that.oc3daigImageLoading.style.display = 'flex';
                        that.oc3daigImageGrid.innerHTML = '';
                        //that.oc3daigImageSelectAll.style.display = 'none';
                        let oc3daigImageError = document.getElementsByClassName('oc3daig-image-error');
                        if (oc3daigImageError.length) {
                            oc3daigImageError[0].remove();
                        }
                        
                        that.oc3daigStartTime = new Date();
                        that.image_generator(queryString, 1, num_images, false, form_action);


                } else {
                    alert(oc3daigParams.languages.error_image);
                }
                return false;
            });
            this.oc3daigImageSaveBtn.addEventListener('click', function (e) {
                var items = [];
                document.querySelectorAll('.oc3daig-image-item input[type=checkbox]').forEach(function (item) {
                    if (item.checked) {
                        items.push(item.getAttribute('data-id'));
                    }
                });
                if (items.length) {
                    that.oc3daigImageConvertBar.style.display = 'block';
                    that.oc3daigImageConvertBar.classList.remove('oc3daig_error');
                    that.oc3daigImageConvertBar.getElementsByTagName('small')[0].innerHTML = '0/' + items.length;
                    that.oc3daigImageConvertBar.getElementsByTagName('span')[0].style.width = 0;
                    that.oc3daigImageMessage.innerHTML = '';
                    oc3daigImageLoadingEffect(that.oc3daigImageSaveBtn,'.oc3daig-img-loader2');
                    that.save_image(items, 0);
                } else {
                    alert(oc3daigParams.languages.select_save_error);
                }
            });
  },
  image_generator : function(data, start, max, multi_steps,form_action){
        let that = this;
        const xhttp = new XMLHttpRequest();
        xhttp.open('POST', oc3daigParams.ajax_url);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        
        xhttp.send(data);
        xhttp.onreadystatechange = function(oEvent) {
            if (xhttp.readyState === 4) {
                if (xhttp.status === 200) {
                    var oc3daig_response = this.responseText;
                    res = JSON.parse(oc3daig_response);
                    if(res.status === 'success'){
                        for(var idx = 0; idx < res.imgs.length; idx++){
                            let idImageBox = idx;
                            if(multi_steps){
                                idImageBox = start -1;
                            }
                            var img = res.imgs[idx];
                            var html = '<div id="oc3daig-image-item-'+idImageBox+'" class="oc3daig-image-item oc3daig-image-item-'+idx+'" data-id="'+idImageBox+'">';
                            if(oc3daigParams.logged_in === '1') {
                                html += '<label><input data-id="' + idImageBox + '" class="oc3daig-image-item-select" type="checkbox" name="image_url" value="' + img + '"></label>';
                            }
                            html += '<input value="'+res.title+'" class="oc3daig-image-item-alt" type="hidden" name="image_alt">';
                            html += '<input value="'+res.title+'" class="oc3daig-image-item-title" type="hidden" name="image_title">';
                            html += '<input value="'+res.title+'" class="oc3daig-image-item-caption" type="hidden" name="image_caption">';
                            html += '<input value="'+res.title+'" class="oc3daig-image-item-description" type="hidden" name="image_description">';
                            html += '<img  onclick="oc3daigImage.imageZoom(' + idImageBox + ')" src="' + img + '">';
                            html += '</div>';
                            that.oc3daigImageGrid.innerHTML += html;
                        }
                        if(multi_steps){
                            if(start === max){
                                oc3daigImageRmLoading(that.oc3daigImageGenerateBtn);
                                //that.oc3daigImageSelectAll.classList.remove('selectall')
                                //that.oc3daigImageSelectAll.innerHTML = oc3daigSelectAllText;
                                //that.oc3daigImageSelectAll.style.display = 'block';
                                that.oc3daigImageLoading.style.display = 'none';
                                that.oc3daigImageSaveBtn.style.display = 'block';
                            }
                            else{
                                that.image_generator(data, start+1, max, multi_steps,form_action)
                            }
                        }
                        else{
                            if(form_action === 'oc3daig_image_generator'){
                                let endTime = new Date();
                                let timeDiff = endTime - that.oc3daigStartTime;
                                timeDiff = timeDiff/1000;
                                data += '&action=oc3daig_image_log&duration='+timeDiff+'&_wpnonce_image_log='+oc3daigImageNonce+'';
                                const xhttp = new XMLHttpRequest();
                                xhttp.open('POST', oc3daigParams.ajax_url);
                                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                xhttp.send(data);
                                xhttp.onreadystatechange = function (oEvent) {
                                    if (xhttp.readyState === 4) {

                                    }
                                }
                            }
                            oc3daigImageRmLoading(that.oc3daigImageGenerateBtn);
                            //that.oc3daigImageSelectAll.classList.remove('selectall');
                            //that.oc3daigImageSelectAll.style.display = 'block';
                            that.oc3daigImageLoading.style.display = 'none';
                            that.oc3daigImageSaveBtn.style.display = 'block';
                        }
                    }
                    else{
                        oc3daigImageRmLoading(that.oc3daigImageGenerateBtn);
                        that.oc3daigImageLoading.style.display = 'none';
                        let errorMessage = document.createElement('div');
                        errorMessage.style.color = '#f00';
                        errorMessage.classList.add('oc3daig-image-error');
                        errorMessage.innerHTML = res.msg;
                        that.oc3daigImageGenerated.prepend(errorMessage);
                        setTimeout(function (){
                            errorMessage.remove();
                        },3000);
                    }
                }
                else{
                    that.oc3daigImageLoading.style.display = 'none';
                    oc3daigImageRmLoading(that.oc3daigImageGenerateBtn);
                    alert('Something went wrong');
                }
                
            }
        }
        
    

    },
    
        save_image : function(items,start){
        let that = this;
        if(start >= items.length){
            that.oc3daigImageConvertBar.getElementsByTagName('small')[0].innerHTML = items.length+'/'+items.length;
            that.oc3daigImageConvertBar.getElementsByTagName('span')[0].style.width = '100%';
            that.oc3daigImageMessage.innerHTML = oc3daigParams.languages.save_image_success;
            oc3daigImageRmLoading(that.oc3daigImageSaveBtn,'.oc3daig-img-loader2');
            setTimeout(function (){
                that.oc3daigImageMessage.innerHTML = '';
            },4000)
        }
        else{
            var id = items[start];
            var item = document.getElementById('oc3daig-image-item-'+id);
            var data = 'action=oc3daig_save_image_media';
            data += '&image_alt='+item.querySelectorAll('.oc3daig-image-item-alt')[0].value;
            data += '&image_title='+item.querySelectorAll('.oc3daig-image-item-title')[0].value;
            data += '&image_caption='+item.querySelectorAll('.oc3daig-image-item-caption')[0].value;
            data += '&image_description='+item.querySelectorAll('.oc3daig-image-item-description')[0].value;
            data += '&image_url='+encodeURIComponent(item.querySelectorAll('.oc3daig-image-item-select')[0].value);
            data +='&nonce='+oc3daigImageSaveNonce;
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', oc3daigParams.ajax_url);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(data);
            xhttp.onreadystatechange = function(oEvent) {
                if (xhttp.readyState === 4) {
                    if (xhttp.status === 200) {
                        var oc3daig_response = this.responseText;
                        res = JSON.parse(oc3daig_response);
                        if(res.status === 'success'){
                            var currentPos = start+1;
                            var percent = Math.ceil(currentPos*100/items.length);
                            that.oc3daigImageConvertBar.getElementsByTagName('small')[0].innerHTML = currentPos+'/'+items.length;
                            that.oc3daigImageConvertBar.getElementsByTagName('span')[0].style.width = percent+'%';
                            that.save_image(items, start+1);
                        }
                        else{
                            that.oc3daigImageConvertBar.classList.add('oc3daig_error');
                            oc3daigImageRmLoading(that.oc3daigImageSaveBtn,'.oc3daig-img-loader2');
                            alert(res.msg);
                        }
                    } else {
                        alert(oc3daigParams.languages.wrong);
                        that.oc3daigImageConvertBar.classList.add('oc3daig_error');
                        oc3daigImageRmLoading(that.oc3daigImageSaveBtn);
                    }
                }
                
                document.querySelectorAll('.oc3daig_modal_content')[0].innerHTML = '';
                let lngth = 'length';
                let oc3daig_overlay = document.querySelectorAll('.oc3daig-overlay');
                console.log(typeof oc3daig_overlay);
                if(oc3daig_overlay &&  lngth in oc3daig_overlay && oc3daig_overlay.length > 0){
                    oc3daig_overlay[0].style.display = 'none';
                }
                let oc3daig_modal = document.querySelectorAll('.oc3daig_modal');
                if(oc3daig_modal && lngth in oc3daig_modal && oc3daig_modal.length > 0){
                    oc3daig_modal[0].style.display = 'none';
                }
                
            }
        }
    },
    imageZoom: function (id){
        var item = document.getElementById('oc3daig-image-item-'+id);
        var alt = item.querySelectorAll('.oc3daig-image-item-alt')[0].value;
        var title = item.querySelectorAll('.oc3daig-image-item-title')[0].value;
        var caption = item.querySelectorAll('.oc3daig-image-item-caption')[0].value;
        var description = item.querySelectorAll('.oc3daig-image-item-description')[0].value;
        var url = item.querySelectorAll('input[type=checkbox]')[0].value;
        document.querySelectorAll('.oc3daig_modal_content')[0].innerHTML = '';
        document.querySelectorAll('.oc3daig-overlay')[0].style.display = 'block';
        document.querySelectorAll('.oc3daig_modal')[0].style.display = 'block';
        document.querySelectorAll('.oc3daig_modal_title')[0].innerHTML = oc3daigParams.languages.edit_image;
        var html = '<div class="oc3daig_grid_form">';
        html += '<div class="oc3daig_grid_form_2"><img src="'+url+'" style="width: 100%"></div>';
        html += '<div class="oc3daig_grid_form_1">';
        html += '<p><label>'+oc3daigParams.languages.alternative+'</label><input type="text" class="oc3daig_edit_item_alt" style="width: 100%" value="'+alt+'"></p>';
        html += '<p><label>'+oc3daigParams.languages.title+'</label><input type="text" class="oc3daig_edit_item_title" style="width: 100%" value="'+title+'"></p>';
        html += '<p><label>'+oc3daigParams.languages.caption+'</label><input type="text" class="oc3daig_edit_item_caption" style="width: 100%" value="'+caption+'"></p>';
        html += '<p><label>'+oc3daigParams.languages.description+'</label><textarea class="oc3daig_edit_item_description" style="width: 100%">'+description+'</textarea></p>';
        html += '<p><div class="oc3daig-custom-loader oc3daig-modal-loader" style="display: none;"></div></p>';
        html += '<button onclick="oc3daigSaveImageData('+id+')" data-id="'+id+'" class="button button-primary oc3daig_edit_image_save" type="button">'+oc3daigParams.languages.save+'</button>';
        html += '</div>';
        html += '</div>';
        document.querySelectorAll('.oc3daig_modal_content')[0].innerHTML = html;
        oc3daigImageCloseModal();
    }
    }
    
    window['oc3daigImage'] = oc3daigImage.init();