<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new UploadSales);
Artisan::add(new MakePdf);
Artisan::add(new ScheduleActivity);
Artisan::add(new TestMail);
Artisan::add(new SendMail);
Artisan::add(new MakeAllocReport);
Artisan::add(new MakeWord);

