let oc3daiabotparameters = false;
let oc3daiacpbindex = 0;
let oc3daig_start_msg = "Hi! How can I help you?";
let oc3daig_chatbot_messages = [{"id":oc3daiaGenId(),"role":"assistant","content":oc3daig_start_msg,"actor":"AI: ","timestamp":new Date().getTime()}];
let oc3daig_chatbot_button_mode = 1;//send
function oc3daiaGetAIButtons(button_type) {
    let cpButtonSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#636a84"><path d="M64 464H288c8.8 0 16-7.2 16-16V384h48v64c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V224c0-35.3 28.7-64 64-64h64v48H64c-8.8 0-16 7.2-16 16V448c0 8.8 7.2 16 16 16zM224 304H448c8.8 0 16-7.2 16-16V64c0-8.8-7.2-16-16-16H224c-8.8 0-16 7.2-16 16V288c0 8.8 7.2 16 16 16zm-64-16V64c0-35.3 28.7-64 64-64H448c35.3 0 64 28.7 64 64V288c0 35.3-28.7 64-64 64H224c-35.3 0-64-28.7-64-64z"/></svg>'
    let buttons = '';
    oc3daiacpbindex = oc3daiacpbindex + 1;
    buttons += '<div class="oc3daia-bot-chatbot-ai-message-buttons">';
        buttons = buttons + '<div class="oc3daia-bot-chatbot-ai-message-copy" title="Click to Copy" id=\'oc3daig_bot_chatbot_ai_message_copy_'+oc3daiacpbindex+'\' onclick="oc3daiaCopyText(\'oc3daig_bot_chatbot_ai_message_copy_'+oc3daiacpbindex+'\',null, '+button_type+'  );" >' +cpButtonSvg + '</div>';
    buttons += '</div>';
    return buttons;
}

function oc3daiaGenId(){
    return Math.random().toString(36).substring(2);
}

function oc3daiaClearText(){
    oc3daig_chatbot_messages = [];
    oc3daig_chatbot_button_mode = 1;
    let sndbtntext = 'Send';
    if(oc3daig_button_config_general_send){
        sndbtntext = oc3daig_button_config_general_send;
    }
    let sendbuttonspan = document.querySelector('.oc3daia-bot-chatbot-send-button span');
    if(sendbuttonspan){
        sendbuttonspan.innerHTML = sndbtntext;
    }
    let usermsgs = document.querySelectorAll('.oc3daia-bot-chatbot-user-message-box');
    while(usermsgs && usermsgs.length > 0){
        usermsgs[0].parentNode.removeChild(usermsgs[0]);
        usermsgs = document.querySelectorAll('.oc3daia-bot-chatbot-user-message-box');
    }
    let aimsgs = document.querySelectorAll('.oc3daia-bot-chatbot-ai-message-box');
    while(aimsgs && aimsgs.length > 0){
        aimsgs[0].parentNode.removeChild(aimsgs[0]);
        aimsgs = document.querySelectorAll('.oc3daia-bot-chatbot-ai-message-box');
    }
    
    
}

function oc3daiaRemoveElementsByClass(className){
    const elements = document.getElementsByClassName(className);
    while(elements.length > 0){
        elements[0].parentNode.removeChild(elements[0]);
    }
}

function oc3daiaSendMessage(e) {
  e.preventDefault();  
  if(oc3daig_chatbot_button_mode !== 1){
      oc3daiaClearText();
      return;
  }
  let userInputEl = document.getElementById("oc3daiabotchatbotpromptinput");
  let userInput = userInputEl.value;
  var loader = document.querySelector('.oc3daia-bot-chatbot-loading-box');

  let mdiv = document.createElement("div");
  mdiv.setAttribute("class", "oc3daia-bot-chatbot-user-message-box");
  mdiv.innerHTML = userInput + oc3daiaGetAIButtons(2);
  let chathistory = document.querySelector('div.oc3daia-bot-chatbot-messages-box');
  chathistory.appendChild(mdiv);
  userInputEl.style.height = "54px";
  let scrolledHeight = document.querySelector('.oc3daia-bot-chatbot-messages-box').scrollHeight;
  let elementHeight = Math.round(document.querySelector('.oc3daia-bot-chatbot-messages-box').offsetHeight);
  let oc3daiaidbot = document.querySelector('#oc3daiaidbot').value;
  loader.style.bottom = (10 + elementHeight - scrolledHeight)+'px';
  loader.style.display = 'block';
  chathistory.scrollTop = chathistory.scrollHeight;
  let msgitem = {"id":oc3daiaGenId(),"role":"user","content":userInput,"actor":"ME: ","timestamp":new Date().getTime()};
  oc3daig_chatbot_messages.push(msgitem);
  let bdy = {'messages':oc3daig_chatbot_messages,'bot_id':oc3daiaidbot,'message':userInput};
  fetch(oc3daiabotparameters.rest_url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      "X-WP-Nonce": oc3daiabotparameters.rest_nonce
    },
    body: JSON.stringify(bdy)
  })
  .then(response => response.text(),error=>{loader.style.display = 'none';})
  .then(data => {
      console.log(data);

    let inputprompt = document.getElementById("oc3daiabotchatbotpromptinput"); // clear input field
    inputprompt.value = '';
    loader.style.display = 'none';
    let chathistory = document.querySelector('div.oc3daia-bot-chatbot-messages-box');
    let dataobj = JSON.parse(data);
    let mdiv = document.createElement("div");
    mdiv.setAttribute("class", "oc3daia-bot-chatbot-ai-message-box");
    if('success' in dataobj && dataobj.success == true && 'reply' in dataobj){
            let reply = dataobj.reply;
            msgitem = {"id":oc3daiaGenId(),"role":"assistant","content":reply,"actor":"AI: ","timestamp":new Date().getTime()};
            oc3daig_chatbot_messages.push(msgitem);
            
            
            mdiv.innerHTML = '<span class="oc3daia-bot-chatbot-ai-response-message">'+reply+'</span>' + oc3daiaGetAIButtons(1);
            chathistory.appendChild(mdiv);
            let sendbuttonspan = document.querySelector('.oc3daia-bot-chatbot-send-button span');
            if(sendbuttonspan){
                let clrbtntxt = 'Clear';
                if(oc3daig_button_config_general_clear){
                    clrbtntxt = oc3daig_button_config_general_clear;
                }
                sendbuttonspan.innerHTML = clrbtntxt;
                oc3daig_chatbot_button_mode = 0;
            }
    }else{
        let errmsg = dataobj.message;
        mdiv.innerHTML = '<span class="oc3daia-bot-chatbot-ai-response-message">'+errmsg+'</span>' + oc3daiaGetAIButtons(1);
        chathistory.appendChild(mdiv);
    }
    
    chathistory.scrollTop = chathistory.scrollHeight;
    //inputprompt.focus();
    oc3daiaSetPromptFocus(inputprompt);
  });
}


function oc3daiaCopyText(idelement,thbut,button_type){
    
        let thisButton = undefined;
        if(idelement.length > 0){
            thisButton = jQuery('#' + idelement);
        }else{
           thisButton =  thbut;
        }
        
        let lxt = '';
        if(button_type === 1){        
            lxt = thisButton.parents(".oc3daia-bot-chatbot-ai-message-box").find('span.oc3daia-bot-chatbot-ai-response-message').text();
        }else{
            lxt = thisButton.parents(".oc3daia-bot-chatbot-user-message-box").text();
        }
        let el = jQuery('<textarea>').appendTo('body').val(lxt).select();
	document.execCommand('copy');
	el.remove();
        jQuery(thisButton).attr('title', 'Copied!');
    
	let copyIcon = jQuery(thisButton).html();
        jQuery(thisButton).html('<span class="oc3daia-copied-result-msg">+</span>');
	setTimeout(function() {
		thisButton.html(copyIcon);
        }, 900);
    
        
  
    }
    
    
function oc3daiaSetPromptFocus(inputprompt){
    let prompt = false;
    if(!inputprompt === undefined){
        prompt = inputprompt;
    }else{
        prompt = document.getElementById("oc3daiabotchatbotpromptinput");
    }
    prompt.focus();
}   

(function($) {
$(document).ready(function () {
    let oc3daiamaximizebtn = $('.oc3daia-bot-chatbot');
    let oc3daiabotelement = document.querySelector(".oc3daia-bot-chatbot");
    if(!oc3daiabotelement){
        return;
    }
    oc3daiabotparameters = JSON.parse(oc3daiabotelement.getAttribute("data-parameters"));
    console.log(oc3daiabotparameters);
    if(oc3daiamaximizebtn){
        //let _this = oc3daiamaximizebtn;
        
         oc3daiamaximizebtn.find('.oc3daia-bot-chatbot-resize-bttn').on('click', function () {
                        let _this = oc3daiamaximizebtn;
			var container = $(this).parents('.oc3daia-bot-chatbot-main-container');
			var bg = _this.find('.oc3daia-bot-chatbot-maximized-bg');
			var src = $(this).attr('src');
			if (!container.hasClass('oc3daia-bot-chatbot-main-container-maximized-view')) {
				$(this).attr('src', src.replace('maximize', 'minimize'));
				$(this).attr('alt', "Minimize");
				container.addClass('oc3daia-bot-chatbot-main-container-maximized-view');
				bg.show();
				$('body').addClass('oc3daia-bot-chatbot-disabled-scroll-body');
			} else {
				$(this).attr('src', src.replace('minimize', 'maximize'));
				$(this).attr('alt', "Maximize");
				container.removeClass('oc3daia-bot-chatbot-main-container-maximized-view');
				bg.hide();
				$('body').removeClass('oc3daia-bot-chatbot-disabled-scroll-body');
			}
                        oc3daiaSetPromptFocus();
		});    
               
        $('.oc3daia-bot-chatbot-end-bttn').on('click' ,function () {
            let _this = oc3daiamaximizebtn;
			var container = $(this).parents('.oc3daia-bot-chatbot-main-container');
			var bg = _this.find('.oc3daia-bot-chatbot-maximized-bg');
			if (container.hasClass('oc3daia-bot-chatbot-main-container-maximized-view')) {
				bg.hide();
				$('body').removeClass('oc3daia-bot-chatbot-disabled-scroll-body');
			}
			container.hide();
			_this.find('.oc3daia-bot-chatbot-closed-view').show();
		});
                
          $('.oc3daia-bot-chatbot-closed-view').on('click', function () {
			$(this).hide();
			var container = $('.oc3daia-bot-chatbot-main-container');
			var bg = $('.ays-assistant-chatbot-maximized-bg');
			if (container.hasClass('oc3daia-bot-chatbot-main-container-maximized-view')) {
				bg.show();
				$('body').addClass('oc3daia-bot-chatbot-disabled-scroll-body');
			}
			container.show();
			//$('.oc3daia-bot-chatbot-prompt-input').focus();
                        oc3daiaSetPromptFocus();
		});
        
        
    }
    
    $('#oc3daiabotchatbotpromptinput').keydown(function (e) {
        
        if ((event.keyCode == 10 || event.keyCode == 13) && event.ctrlKey){
            oc3daiaSendMessage(e);
            return;
        }
        let sendbuttonspan = document.querySelector('.oc3daia-bot-chatbot-send-button span');
            if(sendbuttonspan){
                let sndbtntext = 'Send';
                if(oc3daig_button_config_general_send){
                    sndbtntext = oc3daig_button_config_general_send;
                }
                sendbuttonspan.innerHTML = sndbtntext;
                oc3daig_chatbot_button_mode = 1;
            }
        
        
    });
    
    
    
    $('.oc3daia-bot-chatbot-ai-message-copy').on('click', function(){
			    let thisButton = $(this);
                            oc3daiaCopyText('',thisButton);
  
		});
                

    
	});
		
})(jQuery);           

