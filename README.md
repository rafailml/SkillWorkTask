# Skillwork Task assignment

## Phase 1
You have to prepare a simple API that uses most of the structures that Laravel provides by itself.  

The first part of the task is to make sure that the API that you built up is secured and every API request is being signed with some sort of AuthToken. 
There are many approaches to achieve this. You could do this though:headers, query param, cookies  

Please make sure that every request should be validated and an error is returned if the user has no the right permissions to access it
or it is not logged in.  
Please make sure that you are picking the most pragmatic and robust way.
To be able these secured endpoints to be accessed, we need to allow the users to:
* Register to the platform
* To get a login
* To be able to change his password ( forgotten password)

In order to allow to the admin to create a system user, there are needed some sturcts which populates on demand the database with at least one dummy user.

#### TODO:
1. Every API request is being signed with some sort of AuthToken.
2. Every request should be validated and an error is returned if the user has no the right permissions to access it or it is not logged in.
3. User factory.

#### Phase 1 Acceptance:
1. Exposed endpoint for login POST /v1/api/login
2. Exposed endpoint for register POST /v1/api/sign-in
3. Exposed endpoint for forgotten password POST /v1/api/forgotten
4. Recieved email with some magic link or hasg which needs to be added to the forgotten password endpoint when user change the password. As EMAILER you could use the logger driver of laravel where the email it will be sent to the mail log file.
5. Exposed endoint for change the password POST /v1/api/change-passowrd
6. UnitTests and API tests which ensures the quality of the provided work
7. Using of exceptions where it is needed
8. Using of Resources, Custom Request classes
9. Secure endpoints by CSRF tokens where this is needed


## Phase 2
1. Sign up to the clearbit API (API Reference: https://dashboard.clearbit.com/docs ). There you will find how their API works. An important part, while you are preparing your solution for access to the external source, is the reusability, durability, and isolation of the code responsible for it. Please make sure that you are using the most pragmatic and right way from Laravel's perspective to use a service container such as this.
2. When we put a payload body in POST /v1/api/company where we will be publishing a request for a company that we need information for, the user should receive an approval for that the task is stored and will be processed as soon as possible  
`{`  
   &nbsp;&nbsp;&nbsp;&nbsp;`"company_name": Some text`  
   &nbsp;&nbsp;&nbsp;&nbsp;`"company_domain": Some text`  
`}`
3. When the request is received some event needs to be raised and the task should be sent in the queue where it will be processed later.
4. Some worker should download the data by the ComnpanyAPI (part of the Clearbit API) and save the artifacts to the database.
5. When the company details are being saved to the database, an email needs to be sent to the logged user who requested the information
   for the certain company
6. Expose an endpoint where the user can obtain detailed information for the requested company by the domain used before
7. Expose an endpoint which provides information for a certain task is not yet scrapped or it is ???


#### TODO:
1. API call to ComnpanyAPI
2. Rise event when request is received
3. Send email when job is done

#### Phase 2 Acceptance:
1. Allow user to create a request for delivering infromation through ClearBit API
2. Allow to user to receive on demand by API endpoint all the pieces of scraped information using the domain website of the company
3. Make sure that the requests for download of comapny details will be not waiting all the data to be downlaoded and will be used some workaround to provide this asyncronomus approach.
4. Allow to user to check every created task/request and status for that is being downloaded by the background worker.
5. Use right HTTP status codes in the API Responses
6. Make sure all the resources returned by the API are reusable
7. Make sure it is returned an error when something went wrong
   and this error do not expose sensetive server data which allow to
   hackers to corrupt the server
8. Make sure that all the validations while the user do a request are
   implemented
9. Make sure that the code has at least 80% code coverage.


# To run project
1. `composer install`
2. Edit .env file and change database settings
3. Add Clearbit API key in .env file
4. `php artisan key:generate`
5. Add some jobs
6. `php artisan queue:work --stop-when-empty` - execute queued jobs


### My notes
1. About tokens
   1. I'll use header to transfer the token, 
   2. Cookie needs additional security measures against XSRF
   3. Query params are visible, don't like that
