<?php

/**
 * Explain the basics of content based filtering
 */
function recsys_wb_explain_cb() {
  $title = "<strong><h3>Content Based Filtering</h3></strong>";
  $explanation = "In content based filtering the idea is to match (and recommend
) similar items based on their content. This content information can either be 
extracted automatically or added manually. If we know that the user Bob likes 
fantasy movies (from his user profile), and there is a new fantasy movie 
available we can recommend this movie to Bob. Or if Alice is reading an article 
about politics in the middle east, we can recommend further articles about the 
politics in the middle east to alice. The good thing is, that there is no (at 
least not always) user profile necessary to recommend things. The bad thing 
however is, that most items must be enriched (often manually) with this content 
information (as for example \"this movie is a fantasy movie\")";
  return $title . $explanation;
}

?>