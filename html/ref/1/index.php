<html>
<body>
<h1>1. Getting Started</h1>
<h2>a. What is a server and why do we need one?</h2>
<p>
A server is all about where you want user information, pictures, and application
data (friend requests, messages, etc.) to be stored. You also need code to
instruct the server on how to store and prepare this data. Think of a server as a computer or virtual machine
that is always on and is accessible via an IP address (http://54.148.12.187/) or domain name (www.gocollegedays.com) for whatever
we need it for. 
<br><br>
This server uses Apache to serve files to the web. Files in the directory
/var/www/html are accessible to the public. This very file you're looking 
at right now is a PHP file located at /var/www/html/ref/1/index.php. An index file
is the file Apache looks for when serving a directory (www.gocollegedays.com/ref/1). This index happens to be a PHP file, which
can have code used to instruct the server when this page is loaded by Apache (when you visit this webpage).
<br><br>
If you were to edit this file and refresh this page, you would see the changes you made. You can do this now, however a better
workflow is to first make changes to a local copy of the file, push the changes to our repo, and pull the changes to update the server.
<br><br>
</p>
<h2>b. How to SSH onto the server</h2>
<p>
If you're on Windows, use Putty to connect to gocollegedays.com
<br>
If you're on Mac/Linux, open a new terminal window and enter:
<br><br><span class="code">$ ssh username@gocollegedays.com</span>
<br><br>
</p>
<h2>c. Setting up your 'local' repo</h2>
<p>
Let's setup your local repository, this is where you will work and make changes to the application and push those changes to our repo (collegedays_www.git).
Everyone was invited to our team on Bitbucket (check your ucsd email), you need to be a part of the team to access this repo.
<br><br>
You should already be in your home directory, /home/yourusername/, but to make sure, enter
<br><br><span class="code">$ cd ~</span>
<br><br>
To clone the current application code, enter
<br><br><span class="code">$ git clone https://yourusername@bitbucket.org/creepsrus/collegedays_www.git</span>
<br><br>
You should now have a directory in your home directory named collegedays_www. Enter this directory
and take a look at what's inside. It should look like what's in /var/www/
<br><br>
<h2>d. How to carry your weight</h2>
<p>
You are now able to make your first contribution to the code of our server. Make a directory with the name of your username
in the test directory and enter it.
<br><br><span class="code">$ cd ~</span>
<br><br><span class="code">$ cd collegedays_www/html/test</span>
<br><br><span class="code">$ mkdir yourusername</span>
<br><br><span class="code">$ cd yourusername</span>
<br><br>
Make a new file called index.php using a text editor of your choice. Enter some text in there and save the file.
<br><br><span class="code">$ vim index.php</span>
<br><br>
You should now have a file called index.php in /home/yourusername/collegedays_www/html/test/yourusername<br>
Push your changes to our repository with the following
<br><br><span class="code">$ git add .</span>
<br><br><span class="code">$ git commit -m 'First commit'</span>
<br><br><span class="code">$ git push -u origin master</span>
<br><br>
</p>

<h2>e. Seeing your changes</h2>
<p>
Our collegedays_www repo should now have the changes you just made. You can check by going online to Bitbucket.org
and viewing the changes there.<br><br>
Open up your browser and type in www.gocollegedays.com/test/yourusername/<br><br>
You'll notice that you get a 404 error. This is because you haven't pulled the changes you've made to /var/www/ and the file
doesn't exist here yet.<br>
To pull the changes you've made
<br><br><span class="code">$ cd /var/www/</span>
<br><br><span class="code">$ git pull</span>
<br><br>
You'll be prompted for your bitbucket username and password. When the changes are pulled, refresh your browser and 
you should see your new index.php
<br><br>
</p>

</body>
<style type="text/css">
body
{
    margin-left: 30px;
	margin-right: 30px;
	font-family: 'Helvetica', 'Arial', sans-serif;
}

.code
{
	background: rgb(220,220,220);
	font-family: 'Courier New';
}
</style>
</html>
