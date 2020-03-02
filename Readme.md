# Symfony Export (Demo Project)

The first question I get when applying to Symfony jobs is "How much commercial experience with Symfony do you have?". And yes, even though I have more than 3 years of backend PHP development experience, I've never really had a chance to work with Symfony. I worked with almost all popular CMS, lots of APIs, even Yii and Laravel. For some reason this has never been enough for employers to trust me and let me work with the framework I really wish. That's why I created this little project as a showcase of my abilities.

Let's see what do we have here!

Stack:
- docker
- nginx
- PHP 7.2.3
- MySQL
- MailDev
- Symfony 4
- RabbitMQ

Concept: An app where you can easily export half a million users to a CSV file.

The whole thing runs inside docker, the web server is nginx, so large scale, much technology. Half a million guys are generated using Symfony Fixtures and Faker, just to look cool. By the way, there are also Register/Log In and user/admin features so that you could be sure I'm OK with CRUD and roles and basic stuff.

So, to the fun part. The first and obvious solution for exporting these users to a CSV file was generating the file and returning it as an attachment in a response. However, this doesn't work so well when there are that much users as the download process may take up to 3 minutes, which is 2 minutes and 58 seconds more than most of the app's users are willing to wait. That's why I use Messenger system and RabbitMQ to put export requests in a queue, work on them in the background and send an email with the file when it's ready. 

As simple as that, this should cover a lot of your questions. But if you still have any, feel free to ask!

Hope you enjoyed the project. Thank you for your time and please hire me, I need money for my music career.
Have a good day!