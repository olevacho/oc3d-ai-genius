
/* config correction */

function oc3daigCountInstructionChar(par) {
    let len = par.value.length;
    if (len > 0) {
        document.querySelector('#oc3daig_submit_edit_instruction').disabled = false;//oc3daig_submit_edit_instruction
    } else {
        document.querySelector('#oc3daig_submit_edit_instruction').disabled = true;
    }
}

function oc3daiaSaveChatbotGeneral(e) {
    e.preventDefault();
    
    if(!jQuery){
        alert(oc3daig_jquery_is_not_installed);
        return;
    }
    let genForm = jQuery('#oc3daig_chatbot_gen_form');
    let data = genForm.serialize();
    oc3daigPutGeneralLoader();
    oc3d_performAjax.call(oc3d_general_tab_dynamic, data);

}


function oc3daigToggleInstruction(e, instr_id) {
    e.preventDefault();

    //check input fields before store instruction
    let oc3ddata = {'oc3d_gpt_toggleinstructnonce': oc3daig_toggleinstructionnonce};
    oc3ddata['action'] = 'oc3d_gpt_toggle_instruction';
    oc3ddata['id'] = instr_id;



    oc3d_performAjax.call(oc3d_toggle_instruction_result_dynamic, oc3ddata);

}


function oc3daigEditInstruction(e, instr_id) {
    e.preventDefault();
    let str_instr_id = instr_id + '';
    oc3daig_edited_instruction_id = instr_id;
    document.querySelector('#oc3daig_idinstruction').value = instr_id;//
    document.querySelector('#oc3daig_id_instruction_lbl').innerHTML = 'ID:' + instr_id;
    if (str_instr_id in oc3daig_instructions && 'instruction' in oc3daig_instructions[str_instr_id]) {
        let instruction = oc3daig_instructions[str_instr_id]['instruction'];
        document.querySelector('#oc3daig_instruction').value = instruction;

        let buttons = {'oc3daig_submit_edit_instruction': [1, 1, 'Save'], 'oc3daig_saveasnew_instruction': [1, 1, ''],
            'oc3daig_new_instruction': [1, 1, ''], 'oc3daig_remove_instruction': [1, 1, '']};
        oc3daigToggleButtons(buttons);
        let typeof_instruction = oc3daig_instructions[str_instr_id]['typeof_instruction'];//oc3daig_instruction_type typeof_instruction
        let typeof_instruction_element = document.querySelector('#oc3daig_instruction_type');
        if (typeof_instruction_element) {
            if (typeof_instruction === '1') {
                typeof_instruction_element.value = 1;
            } else if (typeof_instruction === '2') {
                typeof_instruction_element.value = 2;
            }
            typeof_instruction_element.scrollIntoView({behavior: "smooth"});
        }
        let disabled = oc3daig_instructions[str_instr_id]['disabled'];//oc3daig_instruction_type typeof_instruction
        let disabled_element = document.querySelector('#oc3daig_disabled');
        if (disabled_element) {
            if (disabled == 1) {
                disabled_element.checked = true;
            } else {
                disabled_element.checked = false;
            }
        }
    }

}

function oc3daigNewInstruction(e) {
    e.preventDefault();
    document.querySelector('#oc3daig_idinstruction').value = 0;
    oc3daig_edited_instruction_id = 0;
    let oc3daig_instruction_el = document.querySelector('#oc3daig_instruction');

    if (oc3daig_instruction_el) {
        oc3daig_instruction_el.value = '';
    }

    let oc3daig_disabled = document.querySelector('#oc3daig_disabled');//.checked;
    if (oc3daig_disabled) {
        oc3daig_disabled.checked = 0;
    }
    let oc3daig_instruction_type = document.querySelector('#oc3daig_instruction_type');
    if (oc3daig_instruction_type) {
        oc3daig_instruction_type.value = 1;
    }
    let oc3daig_id_instruction_lbl = document.querySelector('#oc3daig_id_instruction_lbl');
    if (oc3daig_id_instruction_lbl) {
        oc3daig_id_instruction_lbl.innerHTML = 'ID:';
    }
    let buttons = {'oc3daig_submit_edit_instruction': [1, 0, 'Add'], 'oc3daig_saveasnew_instruction': [0, 1, ''],
        'oc3daig_new_instruction': [0, 1, ''], 'oc3daig_remove_instruction': [0, 1, '']};
    oc3daigToggleButtons(buttons);
}

function oc3daigStoreNewInstruction(e) {
    e.preventDefault();
    document.querySelector('#oc3daig_idinstruction').value = 0;
    oc3daig_edited_instruction_id = 0;
    oc3daigStoreInstruction(e);
}


function oc3daigChangePerPage(el) {
    document.querySelector('#oc3daig_page').value = 0;
    oc3daigLoadInstructions('#oc3daig_container','left',-70);
}



function oc3daigNextPage(e) {
    e.preventDefault();
    let current_page = document.querySelector('#oc3daig_page').value;
    document.querySelector('#oc3daig_page').value = (+current_page) + 1;
    oc3daigDisablePointerEvents('.oc3dnext.page-numbers');

    oc3daigLoadInstructions('#oc3daig_container','left',-70);
}

function oc3daigPrevPage(e) {
    e.preventDefault();
    let current_page = document.querySelector('#oc3daig_page').value;
    document.querySelector('#oc3daig_page').value = (+current_page) - 1;
    oc3daigDisablePointerEvents('.oc3dprevious.page-numbers');

    oc3daigLoadInstructions('#oc3daig_container','left',-70);
}

function oc3daigDisablePointerEvents(classes = '') {
    let els = document.querySelectorAll(classes);
    let els_l = els.length;
    for (let i = 0; i < els_l; i++) {
        els[i].style.pointerEvents = 'none';//
    }
}

function oc3daigLoadInstructionsE(e) {
    e.preventDefault();
    document.querySelector('#oc3daig_page').value = 0;
    oc3daigLoadInstructions('#oc3daig_container','right',-70);
}

function oc3daigSearchKeyUp(e) {
    e.preventDefault();
    if (e.key === 'Enter' || e.keyCode === 13) {
        document.querySelector('#oc3daig_page').value = 0;
        oc3daigLoadInstructions('.oc3daig_button_container.oc3daig_bloader');
    }

}

function oc3daigLoadInstructions(element_selector,side,distance) {//side can have 
    //values: left , right; distance in pixels

    let oc3ddata = {'oc3d_gpt_loadnonce': oc3d_gpt_loadnonce};
    oc3ddata['action'] = 'oc3d_gpt_load_instruction';
    oc3ddata['instructions_per_page'] = document.querySelector('#instructions_per_page').value;
    oc3ddata['search'] = document.querySelector('#oc3daig_search').value;
    oc3ddata['page'] = document.querySelector('#oc3daig_page').value;
    oc3daigPutInstructionsLoader(element_selector,side,distance);
    oc3d_performAjax.call(oc3d_load_instruction_result, oc3ddata);

}

function oc3daigClearSearch(e) {
    e.preventDefault();
    let search = document.querySelector('#oc3daig_search');
    if (search) {
        search.value = '';
        document.querySelector('#oc3daig_page').value = 0;
        oc3daigLoadInstructions('#oc3daig_container','right',-70);

    }
}
//oc3daig_submit_edit_instruction
function oc3daigStoreInstruction(e) {

    e.preventDefault();
    
    //check input fields before store instruction
    let oc3ddata = {'oc3d_gpt_confinstructnonce': oc3d_gpt_confinstructnonce};
    oc3ddata['action'] = 'oc3d_gpt_conf_store_instruction';

    let oc3daig_instruction_el = document.querySelector('#oc3daig_instruction');

    if (!oc3daig_instruction_el) {
        return;
    }
    oc3daig_instruction_el.classList.remove("oc3daig_error_field");
    let instruction = oc3daig_instruction_el.value;
    if (instruction.length === 0) {
        //oc3daig_error_field
        oc3daig_instruction_el.setAttribute("class", "oc3daig_error_field");
        return;
    }
    oc3ddata['instruction'] = instruction;

    let oc3daig_disabled = document.querySelector('#oc3daig_disabled').checked;
    if (oc3daig_disabled) {
        oc3ddata['disabled'] = 1;
    } else {
        oc3ddata['disabled'] = 0;
    }
    oc3ddata['id_instruction'] = document.querySelector('#oc3daig_idinstruction').value;
    oc3ddata['instruction_type'] = document.querySelector('#oc3daig_instruction_type').value;
    
    if (oc3ddata['id_instruction'] > 0) {
        oc3daigPutInstructionsLoader('#oc3daig_new_instruction','right',30);
        oc3d_performAjax.call(oc3d_edit_instruction_result_dynamic, oc3ddata);
    } else {
        oc3daigPutInstructionsLoader('#oc3daig_submit_edit_instruction','right',30);
        oc3d_performAjax.call(oc3d_new_instruction_result_dynamic, oc3ddata);
    }
}

function oc3daigShowPagination(data) {

    let total_instructions = data['total'];
    let page = data['page'];
    let items_per_page = data['items_per_page'];
    let prev_page = page - 1;

    let show_next = true;
    if ((page * items_per_page) >= +total_instructions) {
        show_next = false;
    }

    let show_prev = true;
    if (prev_page < 1) {
        show_prev = false;
    }
    let prev_page_as = document.querySelectorAll('.oc3dprevious.page-numbers');
    let next_page_as = document.querySelectorAll('.oc3dnext.page-numbers');
    for (let i = 0; i < prev_page_as.length; i++) {
        prev_page_a = prev_page_as[i];
        prev_page_a.style.pointerEvents = '';//
        if (show_prev) {
            prev_page_a.style.display = 'inline-block';
        } else {
            prev_page_a.style.display = 'none';
        }
    }

    for (let i = 0; i < next_page_as.length; i++) {
        next_page_a = next_page_as[i];
        next_page_a.style.pointerEvents = '';//
        if (show_next) {

            next_page_a.style.display = 'inline-block';

        } else {
            next_page_a.style.display = 'none';
        }
    }



    let totals = document.querySelectorAll('.oc3daig_total_instructions');
    for (let i = 0; i < totals.length; i++) {
        let total = totals[i];
        total.innerHTML = 'Total: ' + total_instructions + ' items';
    }
    let page_numbers = document.querySelectorAll('.page-numbers.current');
    for (let i = 0; i < page_numbers.length; i++) {
        let page_number = page_numbers[i];
        page_number.innerHTML = page;
    }
}



let oc3d_load_instruction_result = {

    ajaxBefore: oc3daigShowLoader,

    ajaxSuccess: function (res) {

        if (!'result' in res || res.result != 200) {
            return;
        }
        let pagidata = {'page': res.page, 'items_per_page': res.instructions_per_page, 'total': res.total};//instructions_per_page

        //update appropriate row in table
        oc3daig_instructions = res.js_instructions;
        //oc3daig_edited_instruction_id = 0;
        let tbody = document.querySelector('#oc3daig-the-list');
        if (!tbody) {
            return 0;
        }
        tbody.innerHTML = '';
        let rows = '';
        for (let idx in oc3daig_instructions) {
            let instruction_o = oc3daig_instructions[idx];
            let tr = '<tr class="';
            if (instruction_o.disabled === '1') {
                tr += 'oc3daig_disabled_text';
            }
            tr += '">';
            let td1 = '<td class="id_column">' + instruction_o.id + '</td>';
            let td2 = '<td><a href="#" onclick="oc3daigEditInstruction(event,' + instruction_o.id + ' )" id="oc3daig_instr_href_' + instruction_o.id + '">' + instruction_o.instruction + '</a></td>';
            let typeofinstr = '';
            if (instruction_o.typeof_instruction === '1') {
                typeofinstr = oc3daig_text_edit_label;
            } else {
                typeofinstr = oc3daig_code_edit_label;
            }
            let td3 = '<td class="mvertical"><span id="oc3daig_type_instr_span_' + instruction_o.id + '">' + typeofinstr + '</span></td>';
            let disabled = '';
            if (instruction_o.disabled === '1') {
                disabled = oc3daig_disabled_label;
            } else {
                disabled = oc3daig_enabled_label;
            }
            let td4 = '<td class=""><span id="oc3daig_enabled_span_' + instruction_o.id + '">' + disabled + '</span></td>';
            let td5 = '<td class="oc3daig_user mvertical"><span>' + instruction_o.user_id + '</span></td>';
            let td6 = '<td class="oc3daig_flags_td"><span title="edit" class="dashicons dashicons-edit" onclick="oc3daigEditInstruction(event,' + instruction_o.id + ')"></span> ';
            if (instruction_o.disabled === '1') {
                td6 += '<span title="enable" class="dashicons dashicons-insert" onclick="oc3daigToggleInstruction(event,' + instruction_o.id + ')"></span> ';
            } else {
                td6 += '<span title="disable" class="dashicons dashicons-remove" onclick="oc3daigToggleInstruction(event,' + instruction_o.id + ')"></span> ';
            }
            td6 += '<span title="remove" class="dashicons dashicons-trash"  onclick="oc3daigRemoveRow(event,' + instruction_o.id + ')"></span></td>';
            tr = tr + td1 + td2 + td3 + td4 + td5 + td6 + '</tr>';
            rows = rows + tr;
        }
        tbody.innerHTML = rows;

        oc3daigShowPagination(pagidata);
        let totals = document.querySelectorAll('.oc3daig_total_instructions');
        for (let i = 0; i < totals.length; i++) {
            let total = totals[i];
            total.innerHTML = 'Total: ' + res.total + ' items';
        }

        let new_table_container_height = oc3daigSetTableContainerHeight();//
        if (new_table_container_height > oc3daig_instruction_table_height) {
            oc3daig_instruction_table_height = new_table_container_height;
            let  tbl_div = document.querySelector('#oc3daig_container');
            if (tbl_div) {
                tbl_div.style.height = oc3daig_instruction_table_height + 'px';
            }
        }
    },

    ajaxComplete: oc3daigHideLoader
};


let oc3d_edit_instruction_result_dynamic = {
    ajaxBefore: oc3daigShowLoader,

    ajaxSuccess: function (res) {

        if (!'result' in res || res.result != 200) {
            return;
        }
        //update appropriate row in table
        let modified_instruction = res.new_instruction;
        let modified_instruction_id = modified_instruction.id;
        let modified_instruction_id_str = modified_instruction_id + '';
        if(modified_instruction_id_str in oc3daig_instructions){
            oc3daig_instructions[modified_instruction_id_str]['instruction'] = modified_instruction.instruction;
            oc3daig_instructions[modified_instruction_id_str]['typeof_instruction'] = modified_instruction.instruction_type;
            oc3daig_instructions[modified_instruction_id_str]['disabled'] = modified_instruction.disabled;
        }


        let oc3daig_instr_href = document.querySelector('#oc3daig_instr_href_' + modified_instruction_id);
        if (!oc3daig_instr_href) {
            return;
        }

        oc3daig_instr_href.innerHTML = modified_instruction.instruction;

        let oc3daig_type_instr_span = document.querySelector('#oc3daig_type_instr_span_' + modified_instruction_id);
        if (!oc3daig_type_instr_span) {
            return;
        }

        if (modified_instruction.instruction_type === 2) {
            oc3daig_type_instr_span.innerHTML = oc3daig_code_edit_label;

        } else if (modified_instruction.instruction_type === 1) {
            oc3daig_type_instr_span.innerHTML = oc3daig_text_edit_label;
        }

        let oc3daig_type_instr_disabled = document.querySelector('#oc3daig_enabled_span_' + modified_instruction_id);
        if (!oc3daig_type_instr_disabled) {
            return;
        }


        let oc3daig_tr = oc3daig_instr_href.parentElement.parentElement;//get parent tr element
        if (modified_instruction.disabled === 1) {
            oc3daig_type_instr_disabled.innerHTML = 'disabled';
            oc3daig_tr.classList.add("oc3daig_disabled_text");
            let rmdashicon = oc3daig_tr.querySelector('span.dashicons-remove');
            if (rmdashicon) {
                rmdashicon.classList.add('dashicons-insert');
                rmdashicon.classList.remove('dashicons-remove');
            }
        } else {
            oc3daig_type_instr_disabled.innerHTML = 'enabled';
            oc3daig_tr.classList.remove("oc3daig_disabled_text");
            let insdashicon = oc3daig_tr.querySelector('span.dashicons-insert')
            if (insdashicon) {
                insdashicon.classList.add('dashicons-remove');
                insdashicon.classList.remove('dashicons-insert');
            }
        }


    },

    ajaxComplete: oc3daigHideLoader
};

let oc3d_new_instruction_result_dynamic = {

    ajaxBefore: oc3daigShowLoader,

    ajaxSuccess: function (res) {
        
        if (!'result' in res || res.result != 200) {

            return;

        }
        //update appropriate row in table
        let modified_instruction = res.new_instruction;
        let modified_instruction_id = modified_instruction.id;
        //let modified_instruction_id_str = modified_instruction_id + '';
        if (modified_instruction_id > 0) {
            alert(oc3d_message_instruction_store_success + ':' + modified_instruction_id);
        } else {
            alert(oc3d_message_instruction_store_error);
            return 0;
        }
        let oc3daig_id_instruction_lbl = document.querySelector('#oc3daig_id_instruction_lbl');
        if (oc3daig_id_instruction_lbl) {
            oc3daig_id_instruction_lbl.innerHTML = 'ID:' + modified_instruction_id;
        }

        let oc3daig_idinstruction = document.querySelector('#oc3daig_idinstruction');
        if (oc3daig_idinstruction) {
            oc3daig_idinstruction.value = modified_instruction_id;
        }
        oc3daig_edited_instruction_id = modified_instruction_id;
        let buttons = {'oc3daig_submit_edit_instruction': [1, 1, 'Save'], 'oc3daig_saveasnew_instruction': [1, 1, ''],
            'oc3daig_new_instruction': [1, 1, ''], 'oc3daig_remove_instruction': [1, 1, '']};
        oc3daigToggleButtons(buttons);
        document.querySelector('#oc3daig_page').value = 1;
        oc3daigLoadInstructions('.oc3daig_button_container.oc3daig_bloader');


    },

    ajaxComplete: oc3daigHideLoader
};

let oc3d_toggle_instruction_result_dynamic = {
    ajaxBefore: oc3daigShowLoader,

    ajaxSuccess: function (res) {

        if (!'result' in res || res.result != 200) {

            return;

        }
        //update appropriate row in table
        let modified_instruction = res.new_instruction;
        let modified_instruction_id = modified_instruction.id;
        let modified_instruction_id_str = modified_instruction_id + '';


        let oc3daig_type_instr_disabled = document.querySelector('#oc3daig_enabled_span_' + modified_instruction_id);
        if (!oc3daig_type_instr_disabled) {
            return;
        }
        let oc3daig_instr_href = document.querySelector('#oc3daig_instr_href_' + modified_instruction_id);
        if (!oc3daig_instr_href) {
            return;
        }
        oc3daig_instructions[modified_instruction_id_str]['disabled'] = modified_instruction.disabled;

        let oc3daig_tr = oc3daig_instr_href.parentElement.parentElement;

        if (modified_instruction.disabled === 1) {
            oc3daig_type_instr_disabled.innerHTML = 'disabled';
            oc3daig_tr.classList.add("oc3daig_disabled_text");
            let remove_icon = oc3daig_tr.querySelector('span.dashicons-remove');
            if (remove_icon) {
                remove_icon.classList.add('dashicons-insert');
                remove_icon.classList.remove('dashicons-remove');
            }
        } else {
            oc3daig_type_instr_disabled.innerHTML = 'enabled';
            oc3daig_tr.classList.remove("oc3daig_disabled_text");
            let insert_icon = oc3daig_tr.querySelector('span.dashicons-insert');
            if (insert_icon) {
                insert_icon.classList.add('dashicons-remove');
                insert_icon.classList.remove('dashicons-insert');
            }
        }
        if(modified_instruction_id == oc3daig_edited_instruction_id){//if id of clicked instruction is equal 
            // instruction that is edited in form then we need to change disable status of edited instruction
            let oc3daig_disabled = document.querySelector('#oc3daig_disabled')
            if (oc3daig_disabled) {
                if(modified_instruction.disabled === 1){
                    oc3daig_disabled.checked = true;
                }else{
                    oc3daig_disabled.checked = false;
                }
            }
        }

    },

    ajaxComplete: oc3daigHideLoader
};

function oc3daigToggleButtons(buttons) {
    for (let bid in buttons) {
        let btn = document.querySelector('#' + bid);
        if (!btn) {
            continue;
        }
        if (buttons[bid][0] === 1) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }
        if (buttons[bid][1] === 1) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
        let btncaption = buttons[bid][2];
        if (btncaption.length > 0) {
            btn.innerHTML = btncaption;
        }
    }
}


/*config models and general*/

function oc3daigSetTableContainerHeight() {
    let table_height = document.querySelector('#oc3daig_instructions').offsetHeight;
    //let oc3daig_table_cheight = document.querySelector('#oc3daig_instructions').clientHeight;
    let search_submit_h = document.querySelector('#oc3daig_search_submit').offsetHeight;
    let pagination = document.querySelector('.oc3daig_pagination');
    let pl = 0;
    if (pagination) {
        pl = pagination.offsetHeight;
    }
    let container_height = table_height + search_submit_h + pl + 20;

    return container_height;
}


function oc3daigSaveGeneral(e) {
    e.preventDefault();
    
    if(!jQuery){
        alert(oc3daig_jquery_is_not_installed);
        return;
    }
    let genForm = jQuery('#oc3daig_gen_form');
    let data = genForm.serialize();
    oc3daigPutGeneralLoader();
    oc3d_performAjax.call(oc3d_general_tab_dynamic, data);

}


function oc3daigSaveModels(e) {
    e.preventDefault();
    if(!jQuery){
        alert(oc3daig_jquery_is_not_installed);
        return;
    }
    let genForm = jQuery('#oc3daig_models_form');
    let data = genForm.serialize();
    oc3daigPutModelsLoader();
    oc3d_performAjax.call(oc3d_models_tab_dynamic, data);
}

function oc3daigPutModelsLoader(){
    let targetelement = document.querySelector('#oc3daig_refresh_models');// event.target;
    let loader = document.querySelector('.oc3daig-models-loader');
    
    if(targetelement && loader){
        let parentel = targetelement.parentElement.parentElement;
        let rect = targetelement.getBoundingClientRect();
        if(rect && parentel){
            let parentelrect = parentel.getBoundingClientRect();
            let rght = rect.right - parentelrect.left;
            loader.style.left = (rght+20) + 'px';
        }
    }
}

function oc3daigPutInstructionsLoader(element_selector,side,distance,loader_selector){

    let targetelement = document.querySelector(element_selector);// '.oc3daig_button_container.oc3daig_bloader'
    let loader = null;
    if(loader_selector){
        loader = document.querySelector(loader_selector);
    }else{
        loader = document.querySelector('.oc3daig-instructions-loader');
    }
    if(targetelement && loader){

        let rect = targetelement.getBoundingClientRect();
        let rght = rect.right - rect.left;
        console.log(rect);
        console.log(rght);
        if(rect){
            if(side === 'left'){
                loader.style.left = (Math.round(rect.left) - Math.round(distance)) +  'px';
                loader.style.top = (Math.round(rect.top) - 5) +  'px';
            }
            if(side === 'right'){
                loader.style.left = (Math.round(rect.right) + Math.round(distance)) +  'px';
                loader.style.top = (Math.round(rect.top) - 5) +  'px';
            }
            
        }
        //console.log('left = ' + loader.style.left);
        //console.log('top = ' + loader.style.top);
    }
}

function oc3daigPutGeneralLoader(){
    let targetelement = document.querySelector('.oc3daig_gbutton_container.oc3daig_bloader');// event.target;
    let loader = document.querySelector('.oc3daig-general-loader');
    if(targetelement && loader){

        let rect = targetelement.getBoundingClientRect();
       
        if(rect){
            loader.style.left =  '200px';

        }
    }
}

function oc3daigGetModels(event) {
    event.preventDefault();
    
    if (event.keyCode && event.keyCode === 13) {
        return;
    }


}

function oc3daigShowLoader(loader_selector){

    if(!jQuery){
        return;
        
    }
    
    if(loader_selector){
        jQuery(loader_selector).css('display','grid');
    }else{
        jQuery('.oc3daig-custom-loader').css('display','grid');
    }
    
}

function oc3daigHideLoader(loader_selector){

    if(jQuery){
        if(loader_selector){
            jQuery(loader_selector).hide();
        }else{    
            jQuery('.oc3daig-custom-loader').hide();
        }
    }
}

let oc3d_models_tab_dynamic = {
    ajaxBefore: oc3daigShowLoader,

    ajaxSuccess: function (res) {
        
        
        
    },

    ajaxComplete: oc3daigHideLoader
};

let oc3d_general_tab_dynamic = {
    ajaxBefore: oc3daigShowLoader,

    ajaxSuccess: function (res) {
        
        oc3d_alertResultMessage(res, oc3daig_message_config_general_error, oc3daig_message_config_general_succes1);
        
    },

    ajaxComplete: oc3daigHideLoader
};


function oc3d_alertResultMessage(res, default_error, default_success){
    if (!'result' in res || res.result != 200) {
            if( 'msg' in res){
                alert(res.msg);
            }else{
                alert(default_error);
            }
            return false;
        }
        alert(default_success);
}



function oc3daigStoreConfig(e) {
    e.preventDefault();

}

function oc3daig_preventDefault(event){
        if (event.key === "Enter") {
            event.preventDefault();
        }
    }

    function oc3d_performAjax(data) {
        jQuery.ajax({
            url: oc3daajaxAction,
            type: 'POST',
            dataType: 'json',
            context: this,
            data: data,
            beforeSend: this.ajaxBefore,
            success: this.ajaxSuccess,
            complete: this.ajaxComplete
        });
    }



/* correction metabox */


function oc3daigMetaSelectInstruction(e, instr_id) {
    e.preventDefault();

    oc3daig_edited_instruction_id = instr_id;
    let instruction_element = document.querySelector('#oc3daig_instruction');
    instruction_element.value = oc3daig_instructions[instr_id]['instruction'];//
    instruction_element.scrollIntoView({behavior: "smooth"});

}

function oc3daigMetaNextPageIn(e) {
    e.preventDefault();
    let current_page = document.querySelector('#oc3daig_page').value;
    document.querySelector('#oc3daig_page').value = (+current_page) + 1;
    oc3daigDisablePointerEvents('.oc3dnext.page-numbers');

    oc3daigMetaLoadInstructions();
}

function oc3daigMetaPrevPageIn(e) {
    e.preventDefault();
    let current_page = document.querySelector('#oc3daig_page').value;
    document.querySelector('#oc3daig_page').value = (+current_page) - 1;
    oc3daigDisablePointerEvents('.oc3dprevious.page-numbers');

    oc3daigMetaLoadInstructions();
}

function oc3daigMetaLoadInstructions() {

    let oc3ddata = {'oc3d_gpt_loadnoncec': oc3d_gpt_loadnoncec};
    oc3ddata['action'] = 'oc3d_gpt_load_correct_instruction';
    oc3ddata['instructions_per_page'] = document.querySelector('#instructions_per_page').value;
    oc3ddata['search'] = document.querySelector('#oc3daig_search').value;
    oc3ddata['page'] = document.querySelector('#oc3daig_page').value;
    oc3ddata['show_enabled_only'] = 1;
    oc3d_performAjax.call(oc3d_load_correct_instruction_result, oc3ddata);

}


let oc3d_load_correct_instruction_result = {

    ajaxBefore: oc3daigShowLoader,

    ajaxSuccess: function (res) {

        if (!'result' in res || res.result != 200) {
            return;
        }
        let pagidata = {'page': res.page, 'items_per_page': res.instructions_per_page, 'total': res.total};//instructions_per_page


        oc3daig_instructions = res.js_instructions;
        let tbody = document.querySelector('#oc3daig-the-list');
        if (!tbody) {
            return 0;
        }
        tbody.innerHTML = '';
        let rows = '';
        for (let idx in oc3daig_instructions) {
            
            let instruction_o = oc3daig_instructions[idx];
            let tr = '<tr class="';
            if (instruction_o.disabled === '1') {
                tr += 'oc3daig_disabled_text';
            }
            tr += '">';
            let td1 = '<td class="id_column">' + '<a href="#" onclick="oc3daigMetaSelectInstruction(event,' + instruction_o.id + ' )" >' + instruction_o.id + '</a></td>';
            let td2 = '<td><a href="#" onclick="oc3daigMetaSelectInstruction(event,' + instruction_o.id + ' )" id="oc3daig_instr_href_' + instruction_o.id + '">' + instruction_o.instruction + '</a></td>';
            let typeofinstr = '';
            if (instruction_o.typeof_instruction === '1') {
                typeofinstr = oc3daig_typeofinstr_text;
            } else {
                typeofinstr = oc3daig_typeofinstr_code;
            }
            let td3 = '<td class="mvertical">' + '<a href="#" onclick="oc3daigMetaSelectInstruction(event,' + instruction_o.id + ' )" >' + typeofinstr + '</a></td>';

            let td4 = '';
            let td5 = '';
            let td6 = '';
            tr = tr + td1 + td2 + td3 + td4 + td5 + td6 + '</tr>';
            rows = rows + tr;
        }
        tbody.innerHTML = rows;

        oc3daigShowPagination(pagidata);
        let totals = document.querySelectorAll('.oc3daig_total_instructions');
        for (let i = 0; i < totals.length; i++) {
            let total = totals[i];
            total.innerHTML = 'Total: ' + res.total + ' items';
        }


    },

    ajaxComplete: oc3daigHideLoader
};


    let oc3d_correct_result_dynamic = {
        ajaxBefore: function () {
            oc3daigShowLoader();
        },

        ajaxSuccess: function (res) {
            if ('result' in res && res.result == 200) {
                jQuery('#oc3daig_result_c').val(res.msg);
            } else if ('result' in res && res.result == 404) {
                jQuery('#oc3daig_result_c').val(res.msg);
            }
        },

        ajaxComplete: function () {
            oc3daigHideLoader();
        }
    };

    let oc3d_generate_result_dynamic = {
        ajaxBefore: function () {
            oc3daigShowLoader();
        },

        ajaxSuccess: function (res) {
            if ('result' in res && res.result == 200) {
                jQuery('#oc3daig_response_e').val(res.msg);
            } else if ('result' in res && res.result == 404) {
                jQuery('#oc3daig_response_e').val(res.msg);
            }
        },

        ajaxComplete: function () {
            oc3daigHideLoader();
        }
    };


function oc3daigMetaLoadInstructionsSearch(e) {
    e.preventDefault();
    document.querySelector('#oc3daig_page').value = 0;
    oc3daigMetaLoadInstructions();
}

function oc3daigMetaClearSearch(e) {
    e.preventDefault();
    let search = document.querySelector('#oc3daig_search');
    if (search && search.value.length > 0) {
        search.value = '';
        document.querySelector('#oc3daig_page').value = 0;
        oc3daigMetaLoadInstructions();

    }
}

function oc3daigMetaClearText(e,elementid){
    e.preventDefault();
    let tarea = document.querySelector('#'+elementid);
    if(!tarea){
        return;
    }
    tarea.value = '';

}

function oc3daigMetaCopyToClipboard(e,elementid){
    e.preventDefault();
    let tarea = document.querySelector('#'+elementid);
    if(!tarea){
        return;
    }
    let lxt = tarea.value;
    if (!navigator.clipboard) {
        return;
    }
    
    navigator.clipboard.writeText(lxt).then(function() {
    alert(oc3daig_copy_clipboard_sucess);
  }, function(err) {
    alert(oc3daig_copy_clipboard_fail + ': '+ err);
  });
  
}




function oc3daigMetaSearchKeyUp(e) {
    e.preventDefault();
    if (e.key === 'Enter' || e.keyCode === 13) {
        document.querySelector('#oc3daig_page').value = 0;
        oc3daigMetaLoadInstructions();

    }

}


/*Metabox  Generate tab functions */


    
    let oc3daig_radion_gen = 2;
    let oc3daig_radion_lst2 = ['Deleted', 'User'];
    function oc3daigAddField(plusElement) {

        let displayButton = document.querySelector("#oc3daig_response_td");
        let tbody = document.querySelector('#oc3daig_expert_tbody');

        let oc3diaia_cur_role = 'User';
        // creating the div container.
        for (let i = oc3daig_radion_gen - 1; i > 0; i--) {
            if (oc3daig_radion_lst2[i] === 'User') {
                oc3diaia_cur_role = 'Assistant';
                break;
            }
            if (oc3daig_radion_lst2[i] === 'Assistant') {
                oc3diaia_cur_role = 'User';
                break;
            }

        }

        let tr = document.createElement("tr");
        tr.setAttribute("class", "oc3daig_field");

        let td = document.createElement("td");
        td.setAttribute("colspan", "3");
        // Creating the textarea element.

        let radiodiv = document.createElement("div");

        radiodiv.setAttribute("class", "oc3daig_halfscreen");

        let radiolbl1 = document.createElement("label");
        radiolbl1.innerHTML = oc3daig_generate_assistant;

        let radioel1 = document.createElement("input");
        radioel1.setAttribute("type", "radio");
        radioel1.setAttribute("class", "oc3daig_act");
        radioel1.setAttribute("name", "oc3daig_actor" + oc3daig_radion_gen);
        radioel1.setAttribute("id", "oc3daig_actor_ae_" + oc3daig_radion_gen);
        radioel1.setAttribute("id_gen_val", oc3daig_radion_gen);
        radioel1.setAttribute("value", "Assistant");
        radioel1.setAttribute("onchange", "oc3daigRadioChange(" + oc3daig_radion_gen + ", 'ae')");

        if (oc3diaia_cur_role === 'Assistant') {
            radioel1.setAttribute("checked", true);
        }
        radiolbl1.setAttribute("for", "oc3daig_actor_ae_" + oc3daig_radion_gen);

        let radiodiv2 = document.createElement("div");
        radiodiv2.setAttribute("class", "oc3daig_halfscreen");
        let radiolbl2 = document.createElement("label");
        radiolbl2.innerHTML = oc3daig_generate_user;
        let radioel2 = document.createElement("input");
        radioel2.setAttribute("type", "radio");
        radioel2.setAttribute("class", "oc3daig_act");
        radioel2.setAttribute("name", "oc3daig_actor" + oc3daig_radion_gen);
        radioel2.setAttribute("id", "oc3daig_actor_ue_" + oc3daig_radion_gen);
        radioel2.setAttribute("id_gen_val", oc3daig_radion_gen);
        radioel2.setAttribute("value", "User");
        radioel2.setAttribute("onchange", "oc3daigRadioChange(" + oc3daig_radion_gen + ", 'ue')");

        if (oc3diaia_cur_role === 'User') {
            radioel2.setAttribute("checked", true);
        }
        radiolbl2.setAttribute("for", "oc3daig_actor_ue_" + oc3daig_radion_gen);

        let textareadiv = document.createElement("div");//
        textareadiv.setAttribute("class", "oc3daig_2actor");

        let textarea = document.createElement("textarea");

        textarea.setAttribute("name", "oc3daig_message_e_" + oc3daig_radion_gen);
        textarea.setAttribute("id", "oc3daig_message_e_" + oc3daig_radion_gen);

        // Creating the textarea element.

        let plusminusdiv = document.createElement("div");//
        plusminusdiv.setAttribute("class", "oc3daig_actor");
        // Creating the plus span element.
        let plus = document.createElement("span");
        plus.setAttribute("onclick", "oc3daigAddField(this)");
        let plusText = document.createTextNode("+");
        plus.appendChild(plusText);

        // Creating the minus span element.
        let minus = document.createElement("span");
        minus.setAttribute("onclick", "oc3daigRemoveField(this," + oc3daig_radion_gen + ")");
        let minusText = document.createTextNode("-");
        minus.appendChild(minusText);

        // Adding the elements to the DOM.
        tbody.insertBefore(tr, displayButton);
        tr.appendChild(td);


        radiodiv.appendChild(radioel1);
        radiodiv.appendChild(radiolbl1);
        td.appendChild(radiodiv);


        radiodiv2.appendChild(radioel2);
        radiodiv2.appendChild(radiolbl2);
        td.appendChild(radiodiv2);

        textareadiv.appendChild(textarea);
        td.appendChild(textareadiv);

        plusminusdiv.appendChild(plus);
        plusminusdiv.appendChild(minus);
        td.appendChild(plusminusdiv);




        // Un hiding the minus sign.
        plusElement.nextElementSibling.style.display = "inline-block"; // the minus sign
        // Hiding the plus sign.
        plusElement.style.display = "none"; // the plus sign

        oc3daig_radion_lst2[oc3daig_radion_gen] = oc3diaia_cur_role;
        oc3daig_radion_gen += 1;
    }

    function oc3daigRadioChange(gen_id, suffix) {

        let el_id = 'oc3daig_actor_' + suffix + '_' + gen_id;
        let el_clicked = document.querySelector('#' + el_id);
        if (el_clicked) {
            let e_c_value = el_clicked.value;
            console.log(e_c_value);
            oc3daig_radion_lst2[gen_id] = e_c_value;
            console.log(oc3daig_radion_lst2);

        }

    }

    function oc3daigRemoveField(minusElement, rmidx) {
        minusElement.parentElement.parentElement.parentElement.remove();
        oc3daig_radion_lst2[rmidx] = 'Deleted';
        console.log(oc3daig_radion_lst2);
    }


