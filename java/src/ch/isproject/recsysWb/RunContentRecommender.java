package ch.isproject.recsysWb;

import java.sql.Connection;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.drupal.project.async_command.*;

public class RunContentRecommender extends AsyncCommand {
	
	private static final String SQL_QUESTION_NODE_PARAMETER = "question";
	
	private DrupalConnection drupalConnection;
	private List<Map<String,Object>> documents;
    
    public RunContentRecommender(CommandRecord record, Druplet druplet) {
    	super(record,druplet);

    	this.record = record;
    	this.druplet = druplet;
    	this.drupalConnection = druplet.getDrupalConnection();
    }
    
    public void processRequest() {
    	logger.info("Starting caluclation");
    	String sqlQueryString = "SELECT body_value,entity_id FROM ";
    	sqlQueryString += "field_data_body WHERE bundle=?";
    	try {
    		this.documents = this.drupalConnection.query(sqlQueryString,
    				SQL_QUESTION_NODE_PARAMETER);
		} catch (SQLException e) {
			logger.severe(e.getStackTrace().toString());
		}
    }
    
    @Override
    protected void beforeExecute() {
    	super.beforeExecute();
    	this.processRequest();
    }
    
    @Override
    protected void afterExecute() {
	    super.afterExecute();
	    logger.info("After execute");
    }
    
    @Override
    protected void execute() {
    	super.execute();
    	TFIDFCreator creator = new TFIDFCreator( this.documents, "entity_id", 
    			"body_value", this.record.getString1() );
    	
		Map<Integer, Map<Integer, Double>> vectors = 
				new HashMap<Integer, Map<Integer,Double>>();

    	try {
    		creator.prepareDocuments();
    		
    		vectors = creator.createTFIDFVector();
    		
		} catch (Exception e) {
			logger.severe(e.getStackTrace().toString());
		}

    	int appId = 1;
    	String tableName = "{recsys_wb_tfidf_values}";
    	String insertSql = this.drupalConnection.d("INSERT INTO " + tableName
    			+ "(app_id, entity_id, word_id, tfidf_value, timestamp) " 
    			+ "VALUES(?, ?, ?, ?, ?)");
    	BatchUploader valueUploader;
    	
    	try {
        	Connection connection = this.drupalConnection.getConnection();

        	boolean transactionIsSupported = connection.getMetaData()
        			.supportsTransactionIsolationLevel(
        					Connection.TRANSACTION_READ_COMMITTED);
        	if ( transactionIsSupported ) {
                connection.setTransactionIsolation(
                		Connection.TRANSACTION_READ_COMMITTED);
            }
        	
			valueUploader = new BatchUploader(null, "Insert-Batch", 
					connection, insertSql,
					this.drupalConnection.getMaxBatchSize());
			valueUploader.start();
			
	    	// Write the results to the database
	    	for (Integer documentId : vectors.keySet() ) {
				for (Integer wordId : vectors.get(documentId).keySet()) {
					valueUploader.put(appId, documentId, wordId, 
							vectors.get(documentId).get(wordId),
							"" + System.currentTimeMillis() );
				}
			}
	    	
	    	valueUploader.accomplish();
	    	valueUploader.join();
	    	
	        connection.commit();
	        connection.close();
	    	
		} catch (SQLException e) {
			logger.severe(e.getStackTrace().toString());
		} catch (InterruptedException e) {
			logger.severe(e.getStackTrace().toString());
		}
    }
}

