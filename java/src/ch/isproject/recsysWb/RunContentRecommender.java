package ch.isproject.recsysWb;

import java.sql.SQLException;
import java.util.List;
import java.util.Map;

import org.drupal.project.async_command.*;

public class RunContentRecommender extends AsyncCommand {
	
	private static final String SQL_QUESTION_NODE_PARAMETER = "question";
	
	private DrupalConnection databaseConnection;
	private List<Map<String,Object>> documents;
    
    public RunContentRecommender(CommandRecord record, Druplet druplet) {
    	super(record,druplet);

    	this.record = record;
    	this.druplet = druplet;
    	this.databaseConnection = druplet.getDrupalConnection();
    }
    
    public void processRequest() {
    	logger.info("Starting caluclation");
    	String sqlQueryString = "SELECT body_value,entity_id FROM ";
    	sqlQueryString += "field_data_body WHERE bundle=?";
    	try {
    		this.documents = this.databaseConnection.query(sqlQueryString,
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
    	try {
    		creator.prepareDocuments();
    		
    		Map<Integer, Map<Integer, Double>> vectors;
    		vectors = creator.createTFIDFVector();
    		
    		System.out.println("Vectors: " + vectors + " can be written to database now!");
		} catch (Exception e) {
			logger.warning(e.getStackTrace().toString());
		}
    	logger.info("Execute execute");
    }
    
}

