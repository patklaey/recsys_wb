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
politics in the middle east to Alice. The good thing is, that there is no (at 
least not always) user profile necessary to recommend things. The bad thing 
however is, that most items must be enriched (often manually) with this content 
information (as for example \"this movie is a fantasy movie\").";
  return $title . $explanation;
}

/**
 * Explain the TFIDF function
 */
function recsys_wb_explain_tfidf() {
  $tfidf = 'TF\text{-}IDF(w,d) = TF(w,d) \times IDF(w)';
  $tf = 'TF(w,d) = {freq(w,d) \over max(freq(i,d), i \in otherWords(w,d))}';
  $idf = 'IDF(w) = \log{N \over n(w)}';
  
  $title = "<strong><h3>TF-IDF</h3></strong>";
  $explanation = "<div class='tex2jax'>";
  $explanation .= "TF-IDF stands for Term Frequency-Inverse Document Frequency 
and encodes a document as a vector in an n-dimensional Euclidean space. The 
space dimension is equal to the number of words in the document. As an example 
the TF-IDF value for a word " . mathInline("w") . " in document " 
. mathInline("d") . " is calculated as follows:";
  $explanation .= mathBlock($tf) . mathBlock($idf) . mathBlock($tfidf);
  $explanation .= "Where " . mathInline('freq(w,d)') . " denotes how many times 
the word " . mathInline("w") . " occurs in document " . mathInline("d") . ", "
. mathInline("otherWords(w,d)") . " specifies the set of all words of document "
. mathInline("d") . " except word " . mathInline("w") . ", " . mathInline("N")
. " is the total number of documents and " . mathInline("n(w)") . " is the 
number of documents in which the word " . mathInline("w") . " occurs at least 
once.<br/>";
  $explanation .= "Consider the following two sentences:";
  $explanation .= "<code>This is some text about a text recommender system
</code>";
  $explanation .= "<code>Content based recommender system are cool</code>";
  $explanation .= "Let's have a look at an exmple: " 
. mathInline('TF\text{-}IDF(text,S_1)') . ":";
  $text_tf = 'TF(text,S_1) = {2 \over 2} = 1';
  $text_idf = 'IDF(text) = \log{2 \over 1} = 0.301';
  $text_tfidf = 'TF(text,S_1) \times IDF(text) = 1 \times 0.301 = 0.301';
  $explanation .= mathBlock($text_tf) . mathBlock($text_idf)
. mathBlock($text_tfidf) . mathBlock('TF\text{-}IDF(text,S_1) = 0.301');

  $explanation .= "Let's caluclate " 
. mathInline('TF\text{-}IDF(recommender,S_2)') . ":";
  $recommender_tf = 'TF(recommender,S_2) = {1 \over 1} = 1';
  $recommender_idf = 'IDF(recommender) = \log{2 \over 2} = 0';
  $recommender_tfidf = 'TF(recommender,S_2) \times IDF(recommender) = 1 \times
0 = 0';
  $explanation .= mathBlock($recommender_tf) . mathBlock($recommender_idf) 
. mathBlock($recommender_tfidf) 
. mathBlock('TF\text{-}IDF(recommender,S_2) = 0');
  $explanation .= "As we can see, the TF-IDF value for the word 'recommender' in
sentence 2 is 0. In fact, every word which occurs in every document has an 
TF-IDF value of 0 (as " . mathInline('IDF(word) = \log{N \over N } = 0') . ").";
  return $title . $explanation . "</div>";
}
?>