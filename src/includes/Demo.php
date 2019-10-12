<?php


$pipeline = (new Pipeline)
	->pipe(new TimesTwoStage)
	->pipe(new AddOneStage);

// Returns 21
$pipeline->process($triggerHappyContext);

