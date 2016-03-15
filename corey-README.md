Notes:

  Plan out back end and relationships first...

  DB updates from scratch are in schema/alters.sql

  Other code is in the applications folder -> we have views/controller/model

  One thing that I would love to change is to database the sitter ranking and the review ranking that contribute to the overall rating. This would make sorting the results using ORM easy.

  I have chosen to use jquery UI slider with min and max values. Would be nice to add in label and always show the value of the slider.

  In the applications/database/config.php, the database config may be updated as needed.

  Actual DB is in home / rover.sql

  # rankings were updated with a script
  #http://localhost/rover/sitters/set_rank

  # view search page -> would love to make table prettier eventually
  #http://localhost/rover/sitters/index
