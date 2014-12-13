package ch.isproject.recsysWb;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

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
	
	private static final int X_COORDINATE = 0;
	private static final int Y_COORDINATE = 1;

	private String documentKeyName;
	private String documentValueName;
	private List<Map<String, Object>> documents;
	private String outputFolder = "./output/";
	private Configuration conf = new Configuration();
	private Path tokenizedDocumentPath;
	private Path tfidfPath;
	private Path termFrequencyVectorPath;
	
	// Parameters for the TFIDF Vector generation
	float normalizationPower = PartialVectorMerger.NO_NORMALIZING;
	
	public TFIDFCreator(List<Map<String, Object>> documents, String documentKey, String documentValue ) {
	
		this.documentKeyName = documentKey;
		this.documentValueName = documentValue;
		this.documents = documents;
	}
	
	@SuppressWarnings("deprecation")
	public void prepareDocuments() throws Exception {
		
		FileSystem fs = FileSystem.get(conf);
		Path documentSequencePath = new Path(this.outputFolder,"squence");
		this.tokenizedDocumentPath = new Path(this.outputFolder,
				DocumentProcessor.TOKENIZED_DOCUMENT_OUTPUT_FOLDER);
		this.tfidfPath = new Path(this.outputFolder,"tfidf");
		this.termFrequencyVectorPath = new Path(this.outputFolder 
				+ DictionaryVectorizer.DOCUMENT_VECTOR_OUTPUT_FOLDER);
		
		SequenceFile.Writer writer  = new SequenceFile.Writer(fs, this.conf, 
				documentSequencePath, Text.class, Text.class);
	
		for (Map<String, Object> entry : this.documents) {
			Text id = new Text("" + entry.get(this.documentKeyName));
			Text body = new Text("" + entry.get(this.documentValueName));
			writer.append(id, body);
		}
		
		writer.close();
		
		DocumentProcessor.tokenizeDocuments(documentSequencePath, 
				StandardAnalyzer.class, this.tokenizedDocumentPath, this.conf);
		
	}
	
	public Map<Integer, Map<Integer, Double>> createTFIDFVector() 
			throws Exception {
		
		DictionaryVectorizer.createTermFrequencyVectors(
				this.tokenizedDocumentPath, new Path(this.outputFolder), 
				DictionaryVectorizer.DOCUMENT_VECTOR_OUTPUT_FOLDER, this.conf, 
				1, 1, 0.0f, this.normalizationPower, true, 1, 100, false,
				false);
		
		Pair<Long[], List<Path>> documentFrequencies = TFIDFConverter
				.calculateDF(this.termFrequencyVectorPath, this.tfidfPath, 
						this.conf, 100 );
		
		TFIDFConverter.processTfIdf(this.termFrequencyVectorPath, 
				this.tfidfPath, this.conf,	documentFrequencies, 1, 100, 
				this.normalizationPower, false, false, false, 1);
		
		Path tfidfVectorPath = new Path( this.outputFolder 
				+ "/tfidf/tfidf-vectors/part-r-00000");
		SequenceFileIterable<Writable, Writable> iterable = 
				new SequenceFileIterable<Writable, Writable>(tfidfVectorPath, 
						this.conf);
		
		Map<Integer,Map<Integer, Double>> vectors = 
				new HashMap<Integer, Map<Integer,Double>>();
		
        for (Pair<Writable, Writable> pair : iterable) {
        	
            Map<Integer,Double> tmp = new HashMap<Integer, Double>();
            Integer documentId = Integer.valueOf(pair.getFirst().toString());
            
            String vector = pair.getSecond().toString();
            vector = vector.replaceAll("[{}]", "");

            String[] pairs = vector.split(",");
            for (int i = 0; i < pairs.length; i++) {
				String[] points = pairs[i].split(":");
				tmp.put(Integer.valueOf(points[X_COORDINATE]), 
						Double.valueOf(points[Y_COORDINATE]));
			}
            vectors.put(documentId, tmp);
        }
        
        return vectors;
	}
}
