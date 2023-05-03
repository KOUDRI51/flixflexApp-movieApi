FlixFlex


 FlixFlex is a web application that allows users to browse and search for movies and TV shows, view details about them, and have account for favorites Movies.
Table of Contents:

•	Technologies Used
•	 Getting Started 
•	Usage 
•	Contributing 
•	License 

Technologies Used
  FlixFlex is built using the following technologies:
 
     PHP 8 
     Symfony 6
     MySQL database 
     The Movie Database API (https://www.themoviedb.org/documentation/api)

Getting Started 
 To get started with FlixFlex, follow these steps:

Clone the repository to your local machine:
      * bash Copy code git clone https://github.com/YOUR_USERNAME/flixflex.git Install the necessary dependencies:
      
      Copy code composer install
      
    * Create a new MySQL database and update the .env file with your database credentials:
     
     
     *Create the necessary database tables:
     bash Copy code DATABASE_URL=mysql://db_user:db_pass@127.0.0.1:3306/namedatabase
     
     bash Copy code php bin/console doctrine:migrations:migrate
     
      *Register for an API key from The Movie Database API (https://www.themoviedb.org/documentation/api) and update the .env file with your API key:
     makefile Copy code TMDB_API_KEY=your_api_key
     Start the development server:sql Copy code symfony server:start

Usage

 FlixFlex allows users to browse and search for movies and TV shows, view details about them, and save or edit their favorites.
Browse movies and TV shows To browse movies and TV shows, go to the home page (/). Here you can see the top-rated movies and TV shows, as well as the latest releases.




License 

FlixFlex is Fectory Digitale tech test .





