/* 
    Document   : com_yoorecipe_import.js
    Created on : 2013-6-19, 14:06:31
    Author     : Leon Zhao
    Description: Import APIs JS package
*/

function addInput(){
	$("#addFreeFormButton").hide();
	//$(".saveForm").show();
    var newInput = $('<li><input type="text" name="recipeUrls[]" value="" /><button class="previewButton"  type="button">Test</button><div class="preview">abcde</div></li>');
    $("#listItemInput").find("ul").first().prepend(newInput);
    $("#listItemInput").find("li").first().hide().slideDown();
    var inputNum = +$("#listItemInput > ul").children('li').length;
    if(inputNum === 5){
        $("#addInputButton").remove();
        var message = jQuery('<p class="alert">You have reached the limitation</p>');
        $("#listItemInput").append(message);
    }
    // update the click event for the preview Button
    freshItems();
}

function removeInput(event)
{
	$(this).closest('div').parent().remove();
	if(!$("#listItemInput ul").has('li').length)
	{
		$("#addFreeFormButton").show();
		$(".saveForm").hide();
		hideTips();
	}
}

/**
 * @author Leon Zhao
 * @desc update the preview Button click event
 */
function freshItems(){
    $('#listItemInput ul li').off('click');
    $('#listItemInput ul li').on('click', '.previewButton',slidePre);
    $('#listItemInput ul li').on('click', '.removeInput',removeInput);
	//$('#listItemInput ul li .removeInput').click(removeInput);
}
var liElement;
function slidePre(event){
    liElement = $(this).closest('li');
    if(liElement.find('input').val() != ''){
        event.preventDefault();
        
        if (liElement.find(".preview").is(":hidden")) {
            liElement.find(".preview").html("<p>Loading ... </p>").show();
            $.ajax({
                    type:"POST",
                    url:"/index.php/index.php?option=com_yoorecipe&task=pre_view_url&format=raw",
                    data: {recipe_link : liElement.find('input').val()}
                })
                .done(function(msg) {
                    if(msg){
                        liElement.find('.preview').html(msg);
                        var buttonDiv = $('<li class="li_content"><button type="submit">Upload Recipes</button><button class="removeInput" type="button">Cancel</button></li>');
                        liElement.find('.preview').append(buttonDiv);
						showTips();
                    }else{
                        liElement.find('.preview').html('Failed to load the URL');
						showTips();
                    }
                })
                .fail(function() { alert("error"); })
               .always(function() {
                    liElement.find(".preview").hide();
                    liElement.find(".preview").slideDown();
                });
            } else {
                liElement.find(".preview").slideUp();
            }
    }else{
		showTips();
        alert("The URL is invalid");
        return false;
    }
}

function showTips()
{
	$('#listItemInput').append("<div id='input_tips' style='position: absolute; top: 0px; width: 200px; right: 50px;'>If your Recipe does not upload correctly, please select 'cancel' and upload the recipe manually.</div>");
}

function hideTips()
{
	$('#listItemInput').find('#input_tips').remove();
}

function addFreeForm(){
	$("#addInputButton").hide();
	//$(".saveForm").show();
    var freeFormUl = $('<ul><li class="li_title">Recipe Link:</li><li class="li_content"><input type="text" name="recipeLink[]" /></li><li class="li_title">Recipe Title:</li><li class="li_content"><input type="text" name="recipeTitle[]" /></li><li class="li_title">Images:</li><li class="li_content"><input type="text" name="recipeImage[]" /></li><li class="li_title">Ingredients:</li><li class="li_content"><textarea name="recipeIngredients[]" rows="10" ></textarea></li><li class="li_content"><button type="submit">Upload Recipes</button><button class="removeFreeForm" style="margin: 2px;"  type="button">Cancel</button></li></ul>');
    $("#listItemFreeForm").prepend(freeFormUl);
    $("#listItemFreeForm").find("ul").first().hide().slideDown();
    var freeFormNum = +$("#listItemFreeForm > ul").length;
    if(freeFormNum === 5){
        $("#addFreeFormButton").remove();
        var message = jQuery('<p class="alert">You have reached the limitation</p>');
        $(".addButtonDiv").append(message);
    }
	$("#listItemFreeForm ul li .removeFreeForm").click(removeFreeForm);
}

function removeFreeForm(event)
{
	$(this).closest('ul').remove();
	if(!$("#listItemFreeForm").has('ul').length)
	{
		$("#addInputButton").show();
		$(".saveForm").hide();
	}
}