While this exercise is quite straightforward, some actions may require additional explanations.

For Value Objects I decided to use numeric properties only as validation there was required. We may possibly expand to validate email and probably name too, but I skipped it for simplicity sake.

As I use Eloquent as driver for database models layer, I have installed barryvdh/laravel-ide-helper package to help with fields and query builder methods autocompletion.   

For sending, validations were not required in the task description, but I still decided to create few validation requests. Also text and subject are also parts of request too, as it should be.

I added factories for testing, and covered all invoice logic as required, including testing endpoints and checking. I skipped testing of repository as in this implementation it just calls service methods and does not contain any logic.  
