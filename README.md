# bookshelf-app-api

This is a RESTful API that manages books. It can add, edit, delete, and show books. It is based on [bookshelf-api](https://github.com/gabrield-droid/bookshelf-api.git).

### Warning:
The current configuration allows CORS for all origins, which can be insecure in production environments. To mitigate this, consider implementing authentication or configuring CORS with more restrictive settings.

## Data structure:

Data example:
```json
{
  "id": "gzfYO49jLyE__ooW",
  "name": "Buku A",
  "year": 2010,
  "author": "John Doe",
  "summary": "Lorem ipsum dolor sit amet",
  "publisher": "Gabriel's Publishing",
  "pageCount": 100,
  "readPage": 25,
  "finished": false,
  "reading": false,
  "insertedAt": "2025-06-19T19:07:41.453V",
  "updatedAt": "2025-06-19T19:07:41.453V"
}
```
The properties `id`, `finished`, `insertedAt`, `updatedAt` are managed by the server.
The other properties are input by the client.

## Requirements:
1. PHP with a minimum version 8.1.2.
   You can install it using this command on the terminal:
   ```bash
   sudo apt install php-common php-cli libapache2-mod-php php-pgsql
   ```
   Installing libapache2-mod-php also installs the `Apache2 HTTP Server`.

2. Apache2 HTTP Server
   
   If you have installed `PHP` using the command in step 1 above, Apache2 HTTP Server should already be installed. Otherwise, you can install it or check whether it is installed with this command:
   ```bash
   sudo apt install apache2
   ```
3. PostgreSQL with a minimum version 14.8
   
   This is the database server where the persistent data are stored. If you are using Ubuntu with minimum version 22.04, you can install it or check whether it is installed with this command:
   ```bash
   sudo apt install postgresql
   ```
   The command installs PostgreSQL only. You still need to configure the superuser role before setting up the bookshelf-app-api.

4. Apache2 HTTP Server modules `mod_headers` and `mod_rewrite`.
   You can activate these modules using the following commands:
   ```bash
   sudo a2enmod headers
   sudo a2enmod rewrite
   ``` 

5. Git (for cloning this github repository). You can skip this if you would like to download the repository manually.


## Get the Repository
   Before installing, you have to clone this repository using one of the following commands in the terminal:
   ```bash
   git clone https://github.com/gabrield-droid/bookshelf-app-api.git
   ```
   ```bash
   git clone git@github.com:gabrield-droid/bookshelf-app-api.git
   ```
   ```bash
   gh repo clone gabrield-droid/bookshelf-app-api
   ```
   Alternatively, you can download the ZIP file of the repository and extract it manually.

   Place the repository folder into this directory `/var/www/`.

## Installation
1. Configure the PostgreSQL user credentials and database
   
   Create the PostgreSQL user credentials to work with the bookshelf-app-api (Replace <psql_superuser> with your PostgreSQL superuser. Make sure the superuser has a password so it can be used to log in via an IPv4 local connection.):
   ```bash
   createuser -U <psql_superuser> -h localhost <database_user> -P -d -l
   ```
   You will encounter a dialog asking passwords like this:
   ```
   Enter password for the new role: # Enter the password for the newly created PostgreSQL user
   Enter it again:                  # Enter the password again
   Password:                        # Enter the password of the superuser
   ```

   Create the app database using this command:
   ```bash
   createdb -U <database_user> -h localhost <database_name>
   ```

   Open PostgreSQL shell using the PostgreSQL user you've just created above by running this command on the terminal:
   ```bash
   psql -U <database_user> -h localhost <database_name>
   ```
   It will ask a password. Enter the password you created when creating the PostgreSQL user.

   Inside the PostgreSQL shell run these command:
   ```sql
   -- Create the books table
   CREATE TABLE "books" (
   "id" char(16) primary key,
   "name" varchar(50),
   "year" integer,
   "author" varchar(80),
   "summary" text,
   "publisher" varchar(30),
   "page_count" integer,
   "read_page" integer,
   "finished" boolean,
   "reading" boolean,
   "inserted_at" char(24),
   "updated_at" char(24)
   );
   ```
   
2. Create `db_config.php` file

   Inside the project directory, create `db_config.php` file inside `postgresql` folder:
   ```bash
   sudo touch postgresql/db_config.php
   ```
   To edit the file, run this command:
   ```bash
   sudo nano postgresql/db_config.php
   ```
   In the nano editor, paste the following lines:
   ```php
   <?php
      define("DB_USER", "database_user");
      define("DB_PASS", "database_password");
      define("DB_NAME", "database_name");
   ?>
   ```
   Substitute `database_user`, `database_password`, and `database_name` with the values you defined earlier in the previous step.

   To save, press `Ctrl+X`, then `Y`, and then `enter`.

3. Create the site configuration

   Make a configuration file in the directory `/etc/apache2/sites-available/`. You could name it whatever you like but it is recommended you name it as the name of the repository: `bookshelf-app-api.conf`. To edit the file, open the terminal, go to `/etc/apache2/sites-available`, and run this command on the terminal:
   ```bash
   sudo nano bookshelf-app-api.conf
   ```
   Replace `bookshelf-app-api.conf` with your chosen filename if you named it differently.

   In the Nano editor, paste the following lines:
   ```apache
   <VirtualHost *:80>
	   #ServerName bookshelf-app-api.local

	   ServerAdmin webmaster@localhost
	   DocumentRoot /var/www/bookshelf-app-api

	   Header set Access-Control-Allow-Origin "*"
	   Header set Access-Control-Allow-Headers "*"
	   Header set Access-Control-Allow-Methods "PUT, GET, POST, DELETE, OPTIONS"
	   Header set Access-Control-Max-Age "300"

	   <Directory /var/www/bookshelf-app-api>
		   RewriteEngine On
		   RewriteRule ^([A-Za-z0-9\/_-]+)$ index.php
	   </Directory>

	   ErrorLog ${APACHE_LOG_DIR}/error.log
	   CustomLog ${APACHE_LOG_DIR}/access.log combined
   </VirtualHost>

   # vim: syntax=apache ts=4 sw=4 sts=4 sr noet
   ```
   To save, press `Ctrl+X`, then `Y`, and then `enter`.
   
4. Enable the site configuration file.

   Run the following command to enable the site
   ```bash
   sudo a2ensite bookshelf-app-api.conf
   ```
   Replace `bookshelf-app-api.conf` with your chosen filename if you named it differently.

5. Reload the Apache2 HTTP Server.

   To activate the newly enabled configuration file, you need to reload the Apache2 HTTP Server. Run the following command to reload Apache2 HTTP Server:
   ```bash
   sudo service apache2 reload
   ```

### Post-installation
   Verify if the application is running.

   Run this command below:
   ```bash
   curl -X GET http://localhost:80/books
   ```
   If the output matches the following, then the application is running.
   ```json
   {"status":"success","data":{"books":[]}}
   ```

## Use Cases and their Request and Response Formats
### 1. Saving a book
   Request:
   * Method: **POST**
   * Endpoint: **/books**
   * Body Request:
     ```
     {
        "name": string,
        "year": number,
        "author": string,
        "summary": string,
        "publisher": string,
        "pageCount": number,
        "readPage": number,
        "reading": boolean
     }
     ```
     
   Response:
   <table>
      <thead>
         <tr>
            <th>No.</th>
            <th>Scenario</th>
            <th>Status Code</th>
            <th>Response Body</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td>1</td>
            <td>The client doesn't send the property <code>name</code></td>
            <td rowspan=2><strong>400 (Bad Request)</strong></td>
            <td>
               <pre>
                  <code>            
{
   "status": "fail",
   "message": "Gagal menambahkan buku. Mohon isi nama buku"
}
                  </code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>2</td>
            <td>The <code>readPage</code> value is more than the <code>pageCount</code> value</td>
            <td>     
               <pre>
                  <code>       
{
   "status": "fail",
   "message": "Gagal menambahkan buku. readPage tidak boleh lebih besar dari pageCount"
}
                  </code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>3</td>
            <td>The book is successfully saved</td>
            <td><strong>201 (Created)</strong></td>
            <td>     
               <pre>
                  <code>       
{
   "status": "success",
   "message": "Buku berhasil ditambahkan",
   "data": {
      "bookId": "1L7ZtDUFeGs7VlEt"
   }
}
                  </code>
               </pre>
            </td>
         </tr>
      </tbody>
   </table>

### 2. Showing all the books
   Request:
   * Method: **GET**
   * Endpoint: **/books**
   * Query parameters (optional, for filters):
     * **?name**, shows all books that have the name containing the value of this query.
     * **?reading**, shows all reading books if its value is `1`, or all unread books if its value is `0`.
     * **?finished**, shows all finished books if its value is `1`, or all unfinished books if its value is `0`.
  
   Response:
   <table>
      <thead>
         <tr>
            <th>No.</th>
            <th>Scenario</th>
            <th>Status Code</th>
            <th>Response Body</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td>1</td>
            <td>There are some books</td>
            <td rowspan=2><strong>200 (OK)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "success",
   "data": {
      "books": [
         {
            "id": "Qbax5Oy7L8WKf74l",
            "name": "Buku A",
            "publisher": "Gabriel's Publishing"
         },
         {
            "id": "1L7ZtDUFeGs7VlEt",
            "name": "Buku B",
            "publisher": "Gabriel's Publishing"
         },
         {
            "id": "K8DZbfI-t3LrY7lD",
            "name": "Buku C",
            "publisher": "Gabriel's Publishing"
         }
      ]
   }
}
</code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>2</td>
            <td>There are no books</td>
            <td>
               <pre>
<code>
{
   "status": "success",
   "data": {
      "books": []
   }
}
</code>
               </pre>
            </td>
         </tr>
      </tbody>
   </table>

### 3. Showing book's details
   Request:
   * Method: **GET**
   * Endpoint: **/books/{bookId}**

   Response:
   <table>
      <thead>
         <tr>
            <th>No.</th>
            <th>Scenario</th>
            <th>Status Code</th>
            <th>Response Body</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td>1</td>
            <td>The book's <code>id</code> is found</td>
            <td><strong>200 (OK)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "success",
   "data": {
      "book": {
         "id": "aWZBUW3JN_VBE-9I",
         "name": "Buku A Revisi",
         "year": 2011,
         "author": "Jane Doe",
         "summary": "Lorem Dolor sit Amet",
         "publisher": "Gabriel's Publishing",
         "pageCount": 200,
         "readPage": 26,
         "finished": false,
         "reading": false,
         "insertedAt": "2025-06-19T20:17:40.634V",
         "updatedAt": "2025-06-19T20:25:18.806V"
      }
   }
}
</code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>2</td>
            <td>The book's <code>id</code> is not found</td>
            <td><strong>404 (Not Found)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "fail",
   "message": "Buku tidak ditemukan"
}
</code>
               </pre>
            </td>
         </tr>
      </tbody>
   </table>

### 4. Editing a book
   Request:
   * Method: **PUT**
   * Endpoint: **/books/{bookId}**
   * Body Request:
     ```
     {
        "name": string,
        "year": number,
        "author": string,
        "summary": string,
        "publisher": string,
        "pageCount": number,
        "readPage": number,
        "reading": boolean
     }
     ```
   Response:
   <table>
      <thead>
         <tr>
            <th>No.</th>
            <th>Scenario</th>
            <th>Status Code</th>
            <th>Response Body</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td>1</td>
            <td>The client doesn't send the property <code>name</code></td>
            <td rowspan=2><strong>400 (Bad Request)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "fail",
   "message": "Gagal memperbarui buku. Mohon isi nama buku"
}
</code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>2</td>
            <td>The <code>readPage</code> value is more than the <code>pageCount</code> value</td>
            <td>
               <pre>
<code>
{
   "status": "fail",
   "message": "Gagal memperbarui buku. readPage tidak boleh lebih besar dari pageCount"
}
</code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>3</td>
            <td>The book's <code>id</code> is not found</td>
            <td><strong>404 (Not found)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "fail",
   "message": "Gagal memperbarui buku. Id tidak ditemukan"
}
</code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>4</td>
            <td>The book is successfully updated</td>
            <td><strong>200 (OK)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "success",
   "message": "Buku berhasil diperbarui"
}
</code>
               </pre>
            </td>
         </tr>
      </tbody>
   </table>

### 5. Deleting a book
   Request:
   * Method: **DELETE**
   * Endpoint: **/books/{bookId}**

   Response:
   <table>
      <thead>
         <tr>
            <th>No.</th>
            <th>Scenario</th>
            <th>Status Code</th>
            <th>Response Body</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td>1</td>
            <td>The book's <code>id</code> is not found</td>
            <td><strong>404 (Not Found)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "fail",
   "message": "Buku gagal dihapus. Id tidak ditemukan"
}
</code>
               </pre>
            </td>
         </tr>
         <tr>
            <td>2</td>
            <td>The book is successfully deleted</td>
            <td><strong>200 (OK)</strong></td>
            <td>
               <pre>
<code>
{
   "status": "success",
   "message": "Buku berhasil dihapus"
}
</code>
               </pre>
            </td>
         </tr>
      </tbody>
   </table>

## Example: How to use the API using cURL
#### cURL basic syntax:
```
curl -X {HTTP METHOD} -H "Content-Type: application/json" -d {BODY REQUEST} http://localhost:80/{ENDPOINT}{?query_parameters}
```
The `-H "Content-Type: application/json" -d` can be omitted if there is no body request passed on the request.

#### Example 1: Saving a book
```bash
curl -X POST -H "Content-Type: application/json" -d "{\"name\": \"Buku A\", \"year\": 2010, \"author\": \"John Doe\", \"summary\": \"Lorem ipsum dolor sit amet\", \"publisher\": \"Gabriel's Publishing\", \"pageCount\": 100, \"readPage\": 25, \"reading\": false}" http://localhost:80/books
```

#### Example 2: Showing all books
```bash
curl -X GET http://localhost:80/books
```

#### Example 3: Showing all books (with a query parameter)
```bash
curl -X GET http://localhost:80/books?reading=1
```