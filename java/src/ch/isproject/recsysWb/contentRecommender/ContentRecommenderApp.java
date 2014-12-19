package ch.isproject.recsysWb.contentRecommender;

import org.drupal.project.async_command.*;


@Identifier("contentRecommender")
public class ContentRecommenderApp extends Druplet {
	
    public ContentRecommenderApp(DrupletConfig config) {
        super(config);
        registerCommandClass(RunContentRecommender.class);
    }
	
    public static void main(String[] args) {
        CommandLineLauncher launcher = new CommandLineLauncher(ContentRecommenderApp.class);
        launcher.launch(args);
    }
    
}
