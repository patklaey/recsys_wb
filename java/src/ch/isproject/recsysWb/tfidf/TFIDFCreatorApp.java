package ch.isproject.recsysWb.tfidf;

import org.drupal.project.async_command.*;


@Identifier("TFIDFCreatorApp")
public class TFIDFCreatorApp extends Druplet {
	
    public TFIDFCreatorApp(DrupletConfig config) {
        super(config);
        registerCommandClass(RunTFIDFCreator.class);
    }
	
    public static void main(String[] args) {
        CommandLineLauncher launcher = new CommandLineLauncher(TFIDFCreatorApp.class);
        launcher.launch(args);
    }

}
