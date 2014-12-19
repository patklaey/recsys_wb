package ch.isproject.recsysWb.tfidf;

import java.sql.Connection;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.drupal.project.async_command.AsyncCommand;
import org.drupal.project.async_command.BatchUploader;
import org.drupal.project.async_command.CommandRecord;
import org.drupal.project.async_command.DrupalConnection;
import org.drupal.project.async_command.Druplet;

import ch.isproject.recsysWb.RecsysWbUtil;

public class RunTFIDFCreator extends AsyncCommand {
	
	private static final String SQL_QUESTION_NODE_PARAMETER = "question";
	public static final String TFIDF_TABLE_NAME = "{recsys_wb_tfidf_values}";
	
	
	private DrupalConnection drupalConnection;
	private List<Map<String,Object>> documents;
	private int appId;
	Connection databaseBatchConnection;

    
    public RunTFIDFCreator(CommandRecord record, Druplet druplet) {
    	super(record,druplet);

    	this.record = record;
    	this.druplet = druplet;
    	this.drupalConnection = druplet.getDrupalConnection();
    	
    	// TODO set the appId according to the record
    	this.appId = 1;
    }
    
    public void processRequest() {
    	logger.info("Starting caluclation");
    	String sqlQueryString = "SELECT body_value,entity_id FROM ";
    	sqlQueryString += "field_data_body WHERE bundle=?";
    	try {
    		this.documents = this.drupalConnection.query(sqlQueryString,
    				SQL_QUESTION_NODE_PARAMETER);
		} catch (SQLException e) {
			String message = RecsysWbUtil.getPrintableStacktrace(e);
			logger.severe(message);
		}
    }
    
    private void cleanupDatabase() {
    	try {
	    	this.databaseBatchConnection = this.drupalConnection
	    			.getConnection();
	    	
	    	boolean transactionIsSupported = this.databaseBatchConnection
	    			.getMetaData().supportsTransactionIsolationLevel(
	    					Connection.TRANSACTION_READ_COMMITTED);
	    	if ( transactionIsSupported ) {
	    		this.databaseBatchConnection.setTransactionIsolation(
	            		Connection.TRANSACTION_READ_COMMITTED);
	        }
	    	
	    	String deleteSqlCommand = this.drupalConnection.d("DELETE FROM " 
	    			+ TFIDF_TABLE_NAME + " WHERE app_id = ?");
	    	
	    	BatchUploader cleanupBatchJob = new BatchUploader(null, 
	    			"Delete-Batch", this.databaseBatchConnection, 
	    			deleteSqlCommand, this.drupalConnection.getMaxBatchSize());
	    	cleanupBatchJob.start();
	    	cleanupBatchJob.put(this.appId);
	    	cleanupBatchJob.accomplish();
	    	cleanupBatchJob.join();
    	} 
    	catch ( Exception e ) {
    		String message = RecsysWbUtil.getPrintableStacktrace(e);
			logger.severe(message);
		}
	}
    
    @Override
    protected void beforeExecute() {
    	super.beforeExecute();
    	this.processRequest();
    	this.cleanupDatabase();
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
    		
    		logger.info("Preparing documents");
    		
    		creator.prepareDocuments();
    		
    		logger.info("Documents prepared, going to create TFIDF vectors");
    		
    		vectors = creator.createTFIDFVector();
    		
    		logger.info("TFIDF vectors created, uploading them to database");
    		
		} catch (Exception e) {
			String message = RecsysWbUtil.getPrintableStacktrace(e);
			logger.severe(message);
		}

    	String insertSql = this.drupalConnection.d("INSERT INTO " 
    			+ TFIDF_TABLE_NAME + " (app_id, entity_id, tfidf_vector, " 
    			+ "timestamp) VALUES(?, ?, ?, ?)");
    	BatchUploader valueUploader;
    	
    	try {
        	
			valueUploader = new BatchUploader(null, "Insert-Batch", 
					this.databaseBatchConnection, insertSql,
					this.drupalConnection.getMaxBatchSize());
			valueUploader.start();
			
	    	// Write the results to the database
	    	for (Integer documentId : vectors.keySet() ) {
				valueUploader.put(this.appId, documentId, 
						vectors.get(documentId).toString(),
						"" + System.currentTimeMillis() );
			}
	    	
	    	valueUploader.accomplish();
	    	valueUploader.join();
	    	
	        this.databaseBatchConnection.commit();
	        this.databaseBatchConnection.close();
	        
	    	logger.info("Finished DB upload");
	    	
		} catch (Exception e) {
			String message = RecsysWbUtil.getPrintableStacktrace(e);
			logger.severe(message);
		}
    }

}
