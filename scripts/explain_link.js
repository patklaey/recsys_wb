function display_explain_link(link,selection) {
  var select = document.getElementById("get_recommenation_app_selection");
  var algorithm_id = select.options[selection].value;
  link = link.replace('algorithm_id',algorithm_id);
  document.getElementById("recommender_app_explain_link").innerHTML = link;
}
