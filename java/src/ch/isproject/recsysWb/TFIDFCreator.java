package ch.isproject.recsysWb;

import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.fs.FileSystem;
import org.apache.hadoop.fs.Path;
import org.apache.hadoop.io.SequenceFile;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.io.Writable;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.mahout.common.Pair;
import org.apache.mahout.common.iterator.sequencefile.SequenceFileIterable;
import org.apache.mahout.vectorizer.DictionaryVectorizer;
import org.apache.mahout.vectorizer.DocumentProcessor;
import org.apache.mahout.vectorizer.common.PartialVectorMerger;
import org.apache.mahout.vectorizer.tfidf.TFIDFConverter;

public class TFIDFCreator {

	private String documentKeyName;
	private String documentValueName;
	private List<Map<String, Object>> documents;
	private Logger logger;
	
	public TFIDFCreator(List<Map<String, Object>> documents, String documentKey, String documentValue, Logger logger) {
	
		this.documentKeyName = documentKey;
		this.documentValueName = documentValue;
		this.documents = documents;
		this.logger = logger;
	}
	
	@SuppressWarnings("deprecation")
	public void createTFIDFVector() throws Exception {
		Configuration conf = new Configuration(true);
		FileSystem fs = FileSystem.get(conf);
		String outputFolder = "./output/";
		Path documentSequencePath = new Path(outputFolder,"squence");
		Path tokenizedDocumentPath = new Path(outputFolder,DocumentProcessor.TOKENIZED_DOCUMENT_OUTPUT_FOLDER);
		Path tfidfPath = new Path(outputFolder,"tfidf");
		Path termFrequencyVectorPath = new Path(outputFolder + DictionaryVectorizer.DOCUMENT_VECTOR_OUTPUT_FOLDER);
		
		SequenceFile.Writer writer  = new SequenceFile.Writer(fs, conf, documentSequencePath, Text.class, Text.class);
		
		for (Map<String, Object> entry : this.documents) {
			Text id = new Text("" + entry.get(documentKeyName));
			Text body = new Text("" + entry.get(documentValueName));
			writer.append(id, body);
		}
		
		writer.close();
		
		DocumentProcessor.tokenizeDocuments(documentSequencePath, 
				StandardAnalyzer.class, tokenizedDocumentPath, conf);
		
		float normalizationNorm = PartialVectorMerger.NO_NORMALIZING;
		
		DictionaryVectorizer.createTermFrequencyVectors(tokenizedDocumentPath,
				new Path(outputFolder), 
				DictionaryVectorizer.DOCUMENT_VECTOR_OUTPUT_FOLDER, conf, 1, 1,
				0.0f, normalizationNorm, true, 1, 100, false,
				false);
		
		Pair<Long[], List<Path>> documentFrequencies = TFIDFConverter
				.calculateDF(termFrequencyVectorPath, tfidfPath, conf, 100 );
		
		TFIDFConverter.processTfIdf(termFrequencyVectorPath, tfidfPath, conf, 
				documentFrequencies, 1, 100, normalizationNorm,
				false, false, false, 1);
		
		Path path = new Path( outputFolder + "/tfidf/tfidf-vectors/part-r-00000");
		SequenceFileIterable<Writable, Writable> iterable = new SequenceFileIterable<Writable, Writable>(path, conf);
		
		Map<Long,Map<Integer, String>> vectors = new HashMap<Long, Map<Integer,String>>();
		
        for (Pair<Writable, Writable> pair : iterable) {
        	
            Map<Integer,String> tmp = new HashMap<Integer, String>();
            Long documentId = Long.valueOf(pair.getFirst().toString());
            
            String vector = pair.getSecond().toString();
            vector = vector.replaceAll("[{}]", "");

            String[] pairs = vector.split(",");
            for (int i = 0; i < pairs.length; i++) {
				String[] points = pairs[i].split(":");
				tmp.put(Integer.valueOf(points[0]), points[1]);
			}
            vectors.put(documentId, tmp);
            logger.info("ID: " + documentId + " Size: " + tmp.size());
        }
        
        logger.info("Whole vectors size: " + vectors.size());
	}
}
