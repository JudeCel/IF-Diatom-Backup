var onUpdatedTag = function(user_id, topic_id, tag_value){
	var tag_id = "tag_" + user_id;
	var row_id = "tr_" + user_id;

	var element = document.getElementById(tag_id);
	switch(tag_value){
	case 0:
		element.setAttribute("class", "chatTag tag_unset");
		break;
	case 1:
		element.setAttribute("class", "chatTag tag_set");
		break;
	}

	var row = document.getElementById(row_id);
	row.tag = tag_value;
}