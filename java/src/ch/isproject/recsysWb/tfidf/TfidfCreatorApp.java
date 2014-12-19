package ch.isproject.recsysWb.tfidf;

import org.drupal.project.async_command.*;


@Identifier("tfidfCreatorApp")
public class TfidfCreatorApp extends Druplet {
	
    public TfidfCreatorApp(DrupletConfig config) {
        super(config);
        registerCommandClass(RunTFIDFCreator.class);
    }
	
    public static void main(String[] args) {
        CommandLineLauncher launcher = new CommandLineLauncher(TfidfCreatorApp.class);
        launcher.launch(args);
    }

}
