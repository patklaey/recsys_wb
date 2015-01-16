<?php

/**
 * Explain the basics of content based filtering
 */
function recsys_wb_explain_cb() {
  $title = "<strong><h3>Content Based Filtering</h3></strong>";
  $read_more = l(
    "Read more",
    "readmore",
    array(
      'attributes' => array('target' => '_blank') 
    )
  );
  $explanation = "In content based filtering the idea is to match (and recommend
) similar items based on their content. This content information can either be 
extracted automatically or added manually. If we know that the user Bob likes 
fantasy movies (from his user profile), and there is a new fantasy movie 
available we can recommend this movie to Bob. Or if Alice is reading an article 
about politics in the middle east, we can recommend further articles about the 
politics in the middle east to Alice. The good thing is, that there is no (at 
least not always) user profile necessary to recommend things. The bad thing 
however is, that most items must be enriched (often manually) with this content 
information (as for example \"this movie is a fantasy movie\"). $read_more about
 content based filtering.";
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
  $explanation .= mathBlock($tfidf) . mathBlock($tf) . mathBlock($idf);
  $explanation .= "Where " . mathInline('freq(w,d)') . " denotes how many times 
the word " . mathInline("w") . " occurs in document " . mathInline("d") . ", "
. mathInline("otherWords(w,d)") . " specifies the set of all words of document "
. mathInline("d") . " except word " . mathInline("w") . ", " . mathInline("N")
. " is the total number of documents and " . mathInline("n(w)") . " is the 
number of documents in which the word " . mathInline("w") . " occurs at least 
once.<br/>";
  $explanation .= "Consider the following three sentences:";
  $explanation .= recsys_wb_document_example();
  $explanation .= "Let's have a look at an exmple: " 
. mathInline('TF\text{-}IDF(text,S_1)') . ":";
  $text_tf = 'TF(text,S_1) = {2 \over 2} = 1';
  $text_idf = 'IDF(text) = \log{3 \over 1} = 0.477';
  $text_tfidf = 'TF(text,S_1) \times IDF(text) = 1 \times 0.477 = 0.477';
  $explanation .= mathBlock($text_tf) . mathBlock($text_idf)
. mathBlock($text_tfidf) . mathBlock('TF\text{-}IDF(text,S_1) = 0.477');

  $explanation .= "Let's caluclate " 
. mathInline('TF\text{-}IDF(recommender,S_2)') . ":";
  $recommender_tf = 'TF(recommender,S_2) = {1 \over 1} = 1';
  $recommender_idf = 'IDF(recommender) = \log{3 \over 2} = 0.176';
  $recommender_tfidf = 'TF(recommender,S_2) \times IDF(recommender) = 1 \times
0.176 = 0.176';
  $explanation .= mathBlock($recommender_tf) . mathBlock($recommender_idf) 
. mathBlock($recommender_tfidf) 
. mathBlock('TF\text{-}IDF(recommender,S_2) = 0.176');
  $explanation .= "Last example " . mathInline('TF\text{-}IDF(are,S_3)') . ". As
 'are' is the same verb as 'is' just in another person, it is common to not 
calculate the TF-IDF for 'is' and 'are' but instead the TF-IDF of 'be' so;";
  $be_tf = 'TF(be,S_3) = {1 \over 1} = 1';
  $be_idf = 'IDF(be) = \log{3 \over 3} = 0';
  $be_tfidf = 'TF(be,S_3) \times IDF(be) = 1 \times 0 = 0';
  $explanation .= mathBlock($be_tf) . mathBlock($be_idf) . mathBlock($be_tfidf);
  $explanation .= mathBlock('TF\text{-}IDF(be,S_3) = 0');
  $explanation .= "As we can see, the TF-IDF value for the word 'be' in
sentence 3 is 0. In fact, every word which occurs in every document has an 
TF-IDF value of 0 (as " . mathInline('IDF(word) = \log{N \over N } = 0') . ").";
  return $title . $explanation . "</div>";
}

/**
 * Explain how to measure the similarity between documents
 */
function recsys_wb_explain_content_similarity() {
  $title = "<strong><h3>Content Similarity</h3></strong>";
  $explanation = "<div class='tex2jax'>";
  $explanation .= "The similarity between two (text) documents can be measured 
by comparing their TF-IDF vector representation. Consider again the following 
sentences: ";
  $explanation .= recsys_wb_document_example();
  $explanation .= "To comapre this two sentences, first all TF-IDF values are 
calculated: ";
  $explanation .= recsys_wb_content_similarity_example_table();
  $explanation .= "Then the vectors of words their corresponding TF-IDF values 
are formed. In order to be able to compare those vectors, all words which do not
 occur in the first sentence (document) but in the other, are added to the first
 sentence (document) with an TF-IDF value of 0 (as " . mathInline('TF(w,d) = 0')
. "). If we want to compare Sentence 1 and Sentence 2 those vectors look like";
  $vector1 = 'vector_{S1} = \begin{pmatrix}
  \text{this} & 0.239 \newline
  \text{be} & 0 \newline
  \text{some} & 0.239 \newline
  \text{text} & 0.176 \newline
  \text{about} & 0.239 \newline
  \text{a} & 0.088 \newline
  \text{content} & 0.088 \newline
  \text{based} & 0.088 \newline
  \text{recommender} & 0.088 \newline
  \text{system} & 0.088 \newline
  \text{cool} & 0
\end{pmatrix}';
  $vector2 = 'vector_{S2} = \begin{pmatrix}
  \text{this} & 0 \newline
  \text{be} & 0 \newline
  \text{some} & 0 \newline
  \text{text} & 0.176 \newline
  \text{about} & 0 \newline
  \text{a} & 0.176 \newline
  \text{content} & 0.176 \newline
  \text{based} & 0.176 \newline
  \text{recommender} & 0.176 \newline
  \text{system} & 0.176 \newline
  \text{cool} & 0.176
\end{pmatrix}';

  $explanation .= mathBlock($vector1) . mathBlock($vector2);
  $explanation .= "So one can simply use classical similarity methods (for  
example cosine similarity) to compare those two sentences. Doing these 
calculations will return the following similarities:";
  $style = 'text-align:center;vertical-align:middle';
  $similarity_table_header = array(
    array( 'data' => (''), 'style' => $style ),
    array( 'data' => ('Sentence 1'), 'style' => $style ),
    array( 'data' => ('Sentence 2'), 'style' => $style ),
    array( 'data' => ('Sentence 3'), 'style' => $style )
  );
  $similarity_table_rows = array(
    array( 
      array( 'data' => "Sentence 1", 'style' => $style ),
      array( 'data' => 1, 'style' => $style ),
      array( 'data' => 0.474, 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
    ),
    array( 
      array( 'data' => "Sentence 2", 'style' => $style ),
      array( 'data' => 0.474, 'style' => $style ),
      array( 'data' => 1, 'style' => $style ),
      array( 'data' => 0.069, 'style' => $style ),
    ),
    array( 
      array( 'data' => "Sentence 3", 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
      array( 'data' => 0.069, 'style' => $style ),
      array( 'data' => 1, 'style' => $style ),
    ),
  );
  $explanation .= theme( 
    'table',
    array( 
      'header' => $similarity_table_header, 
      'rows' => $similarity_table_rows, 
      'caption' => "Similarity of the sentences" 
    )
  );
  $explanation .= "As one can see, sentence 1 and sentence 2 have something in 
 common, sentence 1 and sentence 3 have nothing in common at all and sentence 2 
and sentence 3 have very, very little in common.";
  return $title . $explanation . "</div>";
}


/**
 * Function to get some example text for TF-IDF explanation
 */
function recsys_wb_document_example() {
  $example = "<code>This is some text about a content based text recommender ";
  $example .= "system</code>";
  $example .= "<code>A content based text recommender system is cool</code>";
  $example .= "<code>Penguins are cool birds but can't fly</code>";
  return $example;
}

/**
 * Function to display the tfidf values of the example text sentences
 */
function recsys_wb_content_similarity_example_table() {
  $style = 'text-align:center;vertical-align:middle';
  $header = array(
    array( 'data' => ('Word'), 'style' => $style ),
    array( 'data' => t('Sentence 1'),'style' => $style ),
    array( 'data' => t('Sentence 2'),'style' => $style ),
    array( 'data' => t('Sentence 3'),'style' => $style ),    
  );
  $rows = array(
    array( 
      array( 'data' => 'this', 'style' => $style ),
      array( 'data' => 0.239, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'be', 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),
      array( 'data' => 0, 'style' => $style ),      
    ),
    array(
      array( 'data' => 'some', 'style' => $style ),
      array( 'data' => 0.239, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'text', 'style' => $style ),
      array( 'data' => 0.176, 'style' => $style ),
      array( 'data' => 0.176, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'about', 'style' => $style ),
      array( 'data' => 0.239, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'a', 'style' => $style ),
      array( 'data' => 0.088, 'style' => $style ),
      array( 'data' => 0.176, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'content', 'style' => $style ),
      array( 'data' => 0.088, 'style' => $style ),
      array( 'data' => 0.176, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'based', 'style' => $style ),
      array( 'data' => 0.088, 'style' => $style ),
      array( 'data' => 0.176, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'recommender', 'style' => $style ),
      array( 'data' => 0.088, 'style' => $style ),
      array( 'data' => 0.176, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'system', 'style' => $style ),
      array( 'data' => 0.088, 'style' => $style ),
      array( 'data' => 0.176, 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
    ),
    array(
      array( 'data' => 'cool', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => 0.176, 'style' => $style ),
      array( 'data' => 0.176, 'style' => $style ),
    ),
    array(
      array( 'data' => 'penguin', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => 0.477, 'style' => $style ),
    ),
    array(
      array( 'data' => 'bird', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => 0.477, 'style' => $style ),
    ),
    array(
      array( 'data' => 'can\'t', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => 0.477, 'style' => $style ),
    ),
    array(
      array( 'data' => 'fly', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => '-', 'style' => $style ),
      array( 'data' => 0.477, 'style' => $style ),
    ),
  );

  return theme( 
    'table',
    array( 
      'header' => $header, 
      'rows' => $rows, 
      'caption' => "TF-IDF values for the sentences" 
    )
  );
}

?>