package ch.isproject.recsysWb;

import java.util.HashMap;
import java.util.Map;

import org.drupal.project.async_command.*;

public class RunContentRecommender extends AsyncCommand {
	
    public static RunContentRecommender create(long appId, Druplet druplet) {
        return create(appId, null, druplet);
    }

    public static RunContentRecommender create(long appId, String prefFilename, Druplet druplet) {
        Map<String, Object> fields = new HashMap<String, Object>();
        fields.put("id1", appId);
        if (prefFilename != null) {
            fields.put("string1", prefFilename);
        }
        CommandRecord record = CommandRecord.forge(fields);
        return new RunContentRecommender(record, druplet);
    }
    
    public RunContentRecommender(CommandRecord record, Druplet druplet) {
    	super(record,druplet);
    	System.out.println("Super, class RunContentRecommender created with record: " + record.toString() + " and druplet " + druplet.toString());
    }
}
