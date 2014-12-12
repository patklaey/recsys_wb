package ch.isproject.recsysWb;

import java.util.List;
import java.util.Map;

import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.fs.FileSystem;
import org.apache.hadoop.fs.Path;
import org.apache.hadoop.io.SequenceFile;
import org.apache.hadoop.io.Text;
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
	
	public TFIDFCreator(List<Map<String, Object>> documents, String documentKey, String documentValue) {
	
		this.documentKeyName = documentKey;
		this.documentValueName = documentValue;
		this.documents = documents;
		
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
	}
}
