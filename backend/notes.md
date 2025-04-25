# User Journeys



# DB Design
```
restaurants {
	$table->id();
    $table->string('name');
    $table->string('location');
    $table->string('currency')->default('KES');
    $table->json('settings')->nullable();
    $table->timestamps();
}
```
