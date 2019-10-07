Cron Planner
=============

Cron planner is a web tool that provides a graphical visualization of a daily run of all your crontabs.  
The purpose of the tool is to assist anyone who has a lot of crons running wild in the system, organizing them.

You can download the tool, or use it on line at https://goncaloqueiros.net/cron-planner/

Why
-----
The tool came out of the necessity to manage around 800 crons that were spread across 30 files, totaling 100 000 runs every day.  
The idea is that you insert your crontabs in the textbox (you can include comments because they will be stripped out), hit `Go!`and inspect the plan latter.

Imagine that with your current crontabs you get this plan:
![Before](https://raw.githubusercontent.com/ghunti/cronPlanner/master/img/cron_planner_before.png)
You can identify that you have spikes every 15 minutes and lower, but still noticeable spikes every 5 minutes.

You then play with your crontabs, until you find what you think is best for you:
![After](https://raw.githubusercontent.com/ghunti/cronPlanner/master/img/cron_planner_after.png)
You can see that we spread the crontabs more evenly across all minutes of the day. We raise the amount of crons run every minute, but avoided the spikes from the previous setup.

Setup
-----

Download the project:  
`git clone git@github.com:ghunti/cronPlanner.git`  
Run composer inside the project folder:  
`cd cronPlanner; composer install`  
Serve the `index.php` file and enjoy :-)
