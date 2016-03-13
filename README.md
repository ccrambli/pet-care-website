# Rover Coding Project

Rover.com was destroyed in a terrible Amazon and GitHub accident.
Thankfully, no dogs were harmed, but we have to rebuild our site using data we retrieved from the Google search index.
We'd like to:

- Rebuild our sitter profiles and user accounts.
- Recreate a search ranking algorithm
- Build an appealing search results page

You can use any language and frameworks you'd like to complete this project.
We're a Django shop, so if you feel strong with Django, please use that.
Otherwise, use the language and frameworks that you feel will best show your skills.

The work you create here should be representative of code that we'd expect to receive from you if you were hired tomorrow.
Our expectation is that you'll write production quality code including tests.

While not required, we encourage you to to add a readme (or update the existing one) to help us understand your approach work and thought process...design choices, trade-offs, dependencies, etc.

Typically, the project takes 2-4 hours to complete.  There is no time limit, although we don't want to take too much of your time, so we advise people to not spend more than 5 hours on the project.

Finally, this is not a trick project, so if you have any questions, don't hesitate to ask.

## Rebuilding Profiles

We were able to write a script and scrape the Google index for all of the reviews customers have left of sitters.
We have saved that information in the attached CSV.
Using the information in the file, we need to design a database schema and import the data from the .csv file.

Note: If a stay includes multiple dogs, those names will be included in the same column of the CSV "|" delimited.

## Recreating the Search Ranking Algorithm

We need to recreate the ranking of sitters when they are displayed on the site.  Here’s the algorithm we use to rank sitters:

- For each sitter, we calculate Overall Sitter Rank.
- Overall Sitter Rank is a weighted combination of the Sitter Score and Ratings Score.
- Sitter Score is 5 times the fraction of the English alphabet comprised by the disinct letters in what we've recovered of the sitter's name.
- Ratings Score is the average of their stay ratings.
- The Overall Sitter Rank is a weighted average of the Sitter Score and Ratings Score, weighted by the number of stays. When a sitter has no stays, their Overall Sitter Rank is equal to the Sitter Score.  When a sitter has 10 or more stays, their Overall Sitter Rank is equal to the Ratings Score.
- In the event that two or more sitters have the same Overall Sitter Rank, the ordering is unimportant and does not need to be handled.

## Building a Sitter List

We need to display the sitters on a page in order of rank.
This should be easy, simply render a list of sitters.
Each row should display one sitter with their name, photo and the average of their stay ratings.

## Filtering Sitters

Finally, we need to allow customers to filter out sitters on the page with poor average stay ratings.
Without making another request to the server, allow users to filter out sitters whose average ratings is below a user specified value.
It’s okay to use UI controls &mdash; sliders, checkboxes, etc &mdash; that limit the values users can enter.

## Hint for Testing the Search Ranking Algorithm
Suppose there is a sitter whose Sitter Score is 2.5 and gets rating of 5.0 with every stay. Then, their score should
behave like:

| Stay          | Overall Sitter Rank         |
| ------------- | ------------- |
| 0 | 2.50
| 1 | 2.75
| 2 | 3.00
| 3 | 3.25
| 4 | 3.50
| 5 | 3.75
| 6 | 4.00
| 7 | 4.25
| 8 | 4.50
| 9 |  4.75
| 10 | 5.00
| 11 | 5.00
| 12 | 5.00
