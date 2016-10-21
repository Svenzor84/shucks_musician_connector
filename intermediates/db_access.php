<?php
/**
 * Filename:    db_access.php
 * Author:      Steve Ross-Byers
 * Description: Connects to MySQL database for user authorization and data retrieval;
 *              Also includes definitions for functions that access the database
 */

//Creating the Database Connection (PDO)
//Database Name
$db_name = "shucks!";

//DSN (Data Source Name)
$dsn = "mysql:host=localhost;dbname=$db_name";

//Username
$username = 'svenzor';

//Password
$password = '';

//Base URL (makes changing host servers easier)
$base_url = "https://shucks-musician-connector-svenzor.c9users.io/";

//Return email address
$return_address = "steve.rossbyers@gmail.com";

//attempt to connect to the database and handle any exceptions
try {
    
    //PDO Attributes
    $options = [
        
        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        
        ];
        
    //Create PDO object
    $db = new PDO($dsn, $username, $password, $options);
    echo "<!-- Successfully COnnected to $db_name -->";
    
} catch (PDOException $e) {
    
    //Connection Error Output
    echo $e->getMessage();
    
    //Terminate Script
    exit();
    
}

//Database Functions
/**
* Function Name: 	user_login()
* Description:		Authenticates username/email address and password; logs the user in for the current session
* Parameters:		$username, string representation of the user's username (or email address)
* Parameters:		$password, string representation of the user's password
* Returns:			TRUE on successful login, FALSE on failure
*/
function user_login($username, $password) {
    
    //make the Database variable global so it can be used inside the function
    global $db;
    
    //build the query to retrieve all information about the user from the users table
    $sql = "SELECT *
            FROM users
            WHERE (username = :username OR email_address = :email_address)
            AND password = :password";
    
    //prepare the query statement
    $statement = $db->prepare($sql);
    
    //bind arguments as parameters and execute the query
    $statement->execute([
        
        ':username'      => $username,
        ':email_address' => strtolower($username),
        ':password'      => md5($password),
        
    ]);
    
    //if our result has at least one row, the login was successful
    if ($statement->rowcount() > 0) {
        
        //retrieve all of the user's data and set session data before reporting success (return TRUE)
        $row = $statement->fetch();
        $_SESSION[user_id] = $row->user_id;
        $_SESSION[f_name] = $row->f_name;
        $_SESSION[m_name] = $row->m_name;
        $_SESSION[l_name] = $row->l_name;
        $_SESSION[suffix] = $row->suffix;
        $_SESSION[username] = $row->username;
        $_SESSION[password] = $row->password;
        $_SESSION[is_admin] = $row->is_admin;
        $_SESSION[is_cronie] = $row->is_cronie;
        $_SESSION[email_address] = $row->email_address;
        
        //we want to be able to output the date with specific format so first grab the signup-date and convert it to a php time string
        $_SESSION[signup_date] = strtotime($row->signup_date);
        //we will leave this as a php time string so that we can reformat it specifically each time it is displayed
        
        //attempt to get the user's instrument data
        $user_inst = grab_inst($_SESSION[user_id]);
        
        //if the user had relevent instrument data, we can populate our session data with it
        if (is_array($user_inst)) {
            
            //foreach loop to get at instrument data
            foreach($user_inst as $obj) {
                
                //add each instrument as a new key in the inst session array with the user's proficiency level as the value
                $_SESSION[inst][$obj->inst_title][$obj->inst_id] = $obj->prof_level;
            }
        }
        
        //attempt to get the user's style data
        $user_styles = grab_styles($_SESSION[user_id]);
        
        //if the user had relevent style data, we can populate our session data with it
        if (is_array($user_styles)) {
            
            //foreach loop to get at our style data
            foreach($user_styles as $obj) {
                
                //add each style as a new key in the style session array with the user's preference (fave or not) as the value
                $_SESSION[styles][$obj->style_title][$obj->style_id] = $obj->is_fave;
            }
        }
        
        //attempt to get the user's region data
        $user_regions = grab_regions($_SESSION[user_id]);
        
        //if we have relevent region data we can populate our session data
        if (is_array($user_regions)) {
            
            unset($_SESSION[regions]);
            
            foreach($user_regions as $obj) {
                
                $_SESSION[regions][$obj->state_title][$obj->region_title] = $obj->region_id;
            }
        }
        
        return TRUE;
    }
    
    //if the login was not successful, report failure (return FALSE)
    return FALSE;
}

/**
* Function Name: 	grab_inst()
* Description:		Retrieves any instrument data for a given user from the database and returns the results in an array of objects
* Parameters:		$user_id, an int representation of the requested user's id, default is NULL which grabs all instrument data
* Returns:			$instruments (array of objects) if data is present, FALSE on failure
*/
function grab_inst($user_id = NULL) {
    
    //make the database object global so we can use it in the function
    global $db;
    
    if ($user_id != NULL) {
        
        //build the query to grab all relevent instrument data
        $sql = "SELECT inst_title, prof_level, i.inst_id
                FROM user_instruments ui
                JOIN instruments i
                ON ui.inst_id = i.inst_id
                WHERE user_id = :user_id
                ORDER BY inst_title";
            
        //prepare the query statement
        $statement = $db->prepare($sql);
    
        //bind the user_id argument as a SQL parameter
        $statement->execute([
        
            ':user_id' => $user_id,
        
        ]);
        
    } else {
        
        //build the query to grab all instrument data
        $sql = "SELECT *
                FROM instruments
                ORDER BY inst_title";
                
        //prepare the statement
        $statement = $db->prepare($sql);
        
        //execute the query
        $statement->execute();
    }
    //if our results have at least one row, then we found relevent instrument data
    if ($statement->rowcount() > 0) {
        
        //retrieve all the results, build the $instruments array of objects, and return it
        $instruments = $statement->fetchall();
        return $instruments;
    }
    
    //if we found no relevent data, we should return FALSE    
    return FALSE;
}

/**
* Function Name: 	grab_styles()
* Description:		Retrieves any style data for a given user from the database and returns the results in an array of objects
* Parameters:		$user_id, an int representation of the requested user's id, default is NULL which grabs all style data
* Returns:			$styles (array of objects) if data is present, FALSE on failure
*/
function grab_styles($user_id = NULL) {
    
    //make the database object global so we can use it in the function
    global $db;
    
    if ($user_id != NULL) {
        
        //build the query to grab all relevent style data
        $sql = "SELECT style_title, is_fave, s.style_id
                FROM user_styles us
                JOIN styles s
                ON us.style_id = s.style_id
                WHERE user_id = :user_id
                ORDER BY style_title";
            
        //prepare the query statement
        $statement = $db->prepare($sql);
    
        //bind the user_id argument as a SQL parameter
        $statement->execute([
        
            ':user_id' => $user_id,
        
        ]);
    
    } else {
        
        //build the query to grab all style data
        $sql = "SELECT *
                FROM styles
                ORDER BY style_title";
                
        //prepare the statement
        $statement = $db->prepare($sql);
        
        //execute the query
        $statement->execute();
        
    }
    //if our results have at least one row, then we found relevent style data
    if ($statement->rowcount() > 0) {
        
        //retrieve all the results, build the $styles array of objects, and return it
        $styles = $statement->fetchall();
        return $styles;
    }
    
    //if we found no relevent data, we should return FALSE    
    return FALSE;
}

/**
* Function Name: 	grab_regions()
* Description:		Retrieves any region data for a given user from the database and returns the results in an array of arrays
* Parameters:		$user_id, an int representation of the requested user's id, default is NULL which grabs all regions
* Returns:			$regions (array of arrays) if data is present, FALSE on failure
*/
function grab_regions($user_id = NULL) {
    
    //make the database object global so we can use it in the function
    global $db;
    
    if ($user_id != NULL) {
        
        //build the query to grab all relevent region data
        $sql = "SELECT region_title, state_title, r.region_id, r.state_id
                FROM user_regions ur
                JOIN regions r
                ON ur.region_id = r.region_id
                JOIN states s
                ON r.state_id = s.state_id
                WHERE user_id = :user_id
                ORDER BY state_title, region_title";
            
        //prepare the query statement
        $statement = $db->prepare($sql);
    
        //bind the user_id argument as a SQL parameter
        $statement->execute([
        
            ':user_id' => $user_id,
        
        ]);
    
    } else {
        
        //build the query to grab ALL region data
        $sql = "SELECT region_title, state_title, r.region_id, r.state_id
                FROM regions r
                JOIN states s
                ON r.state_id = s.state_id
                ORDER BY state_title, region_title";
                
        //prepare statement
        $statement = $db->prepare($sql);
        
        //execute the query
        $statement->execute();
    }
    
    //if our results have at least one row, then we found relevent region data
    if ($statement->rowcount() > 0) {
        
        //retrieve all the results, build the $regions array of arrays, and return it
        //the array is built to group all values by the second column, which is state title
        //therefore the array structure is $regions[state][county/region]
        //$regions = $statement->fetchall(PDO::FETCH_COLUMN|PDO::FETCH_GROUP, 1);
        $regions = $statement->fetchall();
        return $regions;
    }
    
    //if we found no relevent data, we should return FALSE    
    return FALSE;
}

/**
* Function Name: 	add_user()
* Description:		Takes in values from the Create Account form and adds them into the database as a new user
* Parameters:		$user_vals, an associated array containing the values from the Create Account form
* Returns:			$user_id (the new user's id number) on success, FALSE on failure
*/
function add_user($user_vals) {
    
    //make the databse object global, so we can use it in the function
    global $db;
    
    //build the query to add the new user's data to the database
    $sql = "INSERT INTO users (f_name, l_name, username, password, email_address)
            VALUES (:f_name, :l_name, :username, :password, :email_address)";
            
    //preapare the query statement
    $statement = $db->prepare($sql);
    
    //bind our argument values as the inserted values and execute the query
    $statement->execute([
        
        ':f_name'        => $user_vals[f_name],
        ':l_name'        => $user_vals[l_name],
        ':username'      => $user_vals[new_username],
        ':password'      => md5($user_vals[new_password]),
        ':email_address' => strtolower($user_vals[new_email]),
        
    ]);
    
    unset($sql);
    unset($statement);
    
    //build the query to grab the recently added user's id number
    $sql = "SELECT user_id
            FROM users
            WHERE username = :username";
            
    //prepare the query statement
    $statement = $db->prepare($sql);
    
    //bind our username value to the recently added username and execute the query
    $statement->execute([
    
        ':username' => $user_vals[new_username],
        
    ]);
    
    //if our results have a value, then the user was added successfully
    if ($statement->rowcount() > 0) {
        
        //grab the single row result from our query and store the user_id
        $row = $statement->fetch();
        $user_id = $row->user_id;
        
        //if a Middle Name was provided, set up a query and update the database
        if (isset($user_vals[m_name])) {
            
            $sql = "UPDATE users
                    SET m_name = :value
                    WHERE user_id = :user_id";
                    
            //prepare the query statement
            $statement = $db->prepare($sql);
            
            //bind the values and execute
            $statement->execute([
                
                ':value'   => $user_vals[m_name],
                ':user_id' => $user_id,
                
            ]);
        }
        
        //if a Suffix was provided, set up a query and update the database
        if (isset($user_vals[suffix])) {
        
            $sql = "UPDATE users
                    SET suffix = :value
                    WHERE user_id = :user_id";
                    
            //prepare the query statement
            $statement = $db->prepare($sql);
            
            //bind the values and execute
            $statement->execute([
                
                ':value'   => $user_vals[suffix],
                ':user_id' => $user_id,
                
            ]);
        }
        
        //and return the user id value
        return $user_id;
        
    } else {
        
        //otherwise, the user was not properly added to the database, so we return FALSE
        return FALSE;
    }
}

/**
* Function Name: 	grab_users()
* Description:		Searches the users table for data given specific search parameters (or not)
* Parameters:		$query, a string/array representation of the search criteria requested, default is NULL which returns all data
* Returns:			$user_data, an array containing the requested data, or FALSE on an unsuccessful search
*/
function grab_users($query = NULL) {
    
    //set up the database object as global
    global $db;
    
    //if the sort variable has been set (the user has sorted the table) then we need to use the previous query instead of building a new one
    if(isset($_GET['sort'])) {
        
        //set the previous query as the current query
        $sql = $_SESSION[last_query];
        
        //since our user has clicked on a sorting link, we need to add an order by clause
        if ($_GET['sort'] == 'user_id') {
            
            $sql = $sql . " ORDER BY user_id";
            
        } else if ($_GET['sort'] == 'user_id_inv') {
            
            $sql = $sql . " ORDER BY user_id DESC";
            
        } else if ($_GET['sort'] == 'f_name') {
            
            $sql = $sql . " ORDER BY f_name";
            
        } else if ($_GET['sort'] == 'f_name_inv') {
            
            $sql = $sql . " ORDER BY f_name DESC";
            
        } else if ($_GET['sort'] == 'm_name') {
            
            $sql = $sql . " ORDER BY m_name";
            
        } else if ($_GET['sort'] == 'm_name_inv') {
            
            $sql = $sql . " ORDER BY m_name DESC";
            
        } else if ($_GET['sort'] == 'l_name') {
            
            $sql = $sql . " ORDER BY l_name";
            
        } else if ($_GET['sort'] == 'l_name_inv') {
            
            $sql = $sql . " ORDER BY l_name DESC";
            
        } else if ($_GET['sort'] == 'suffix') {
            
            $sql = $sql . " ORDER BY suffix DESC";
            
        } else if ($_GET['sort'] == 'suffix_inv') {
            
            $sql = $sql . " ORDER BY suffix";
            
        } else if ($_GET['sort'] == 'username') {
            
            $sql = $sql . " ORDER BY username";
            
        } else if ($_GET['sort'] == 'username_inv') {
            
            $sql = $sql . " ORDER BY username DESC";
            
        } else if ($_GET['sort'] == 'signup_date') {
            
            $sql = $sql . " ORDER BY signup_date";
            
        } else if ($_GET['sort'] == 'signup_date_inv') {
            
            $sql = $sql . " ORDER BY signup_date DESC";
            
        } else if ($_GET['sort'] == 'email_address') {
            
            $sql = $sql . " ORDER BY email_address";
            
        } else if ($_GET['sort'] == 'email_address_inv') {
            
            $sql = $sql . " ORDER BY email_address DESC";
            
        } else if ($_GET['sort'] == 'is_admin') {
            
            $sql = $sql . " ORDER BY is_admin DESC";
            
        } else if ($_GET['sort'] == 'is_admin_inv') {
            
            $sql = $sql . " ORDER BY is_admin";
            
        } else if ($_GET['sort'] == 'is_cronie') {
            
            $sql = $sql . " ORDER BY is_cronie DESC";
            
        } else if ($_GET['sort'] == 'is_cronie_inv') {
            
            $sql = $sql . " ORDER BY is_cronie";
            
        }
        
        //prepare the query statement
        $statement = $db->prepare($sql);
        
        //if the last vals variable is set, we need to pass it into the execute function
        if (isset($_SESSION[last_vals])) {
            
            $statement->execute($_SESSION[last_vals]);
            
        //otherwise, we do not need any bound values
        } else {
            
            $statement->execute();

        }
        
        //if the query results consist of at least 1 row we have a successful search
        if ($statement->rowcount() > 0) {
            
            //save the results and return them in the user data variable
            $user_data = $statement->fetchall();
            return $user_data;
        
        //otherwise, return false
        } else {
            
            return FALSE;
        }
        
    } else {
    
        //set up the basic query to get all user data
        $sql = "SELECT *
                FROM users";
                
        //the default condition searches the database for ALL user data
        if ($query == NULL) {
                    
            //prepare the query statement
            $statement = $db->prepare($sql);
            
            //execute
            $statement->execute();
            
            //after executing the query, save it as the most recent query
            $_SESSION[last_query] = $sql;
            
            //if the result is at least a single row
            if ($statement->rowcount() > 0) {
            
                //save the results in the user_data variable and return
                $user_data = $statement->fetchall();
                return $user_data;
            
            //if there are no results
            } else {
            
                return FALSE;
            }
        
        //when a query is provided, only specific user data is returned
        } else {
        
            //create an additive string that will build the rest of the sql query and set up our binding array at the same time
            foreach($query as $key => $string) {
                
                $add = $add . " OR f_name LIKE :f_name$key
                                OR l_name LIKE :l_name$key
                                OR m_name LIKE :m_name$key
                                OR user_id LIKE :user_id$key
                                OR username LIKE :username$key
                                OR email_address LIKE :email_address$key";
                
                (array)$bind_vals2 = (array)$bind_vals;
                                
                $bind_vals = [
                    
                    ":f_name$key"        => "%" . $string . "%",
                    ":l_name$key"        => "%" . $string . "%",
                    ":m_name$key"        => "%" . $string . "%",
                    ":user_id$key"       => "%" . $string . "%",
                    ":username$key"      => "%" . $string . "%",
                    ":email_address$key" => "%" . $string . "%",
                    
                ];
                
                $bind_vals = $bind_vals2 + $bind_vals;
            }
            
            //now we need to trim off the first "AND" from our additive string and add "WHERE" in its place
            $add = substr($add, 3);
            $add = " WHERE" . $add;
            
            //now we can add the additive string to our query
            $sql = $sql . $add;
            
            
            
            //prepare the query statement
            $statement = $db->prepare($sql);
            
            //bind all of our search strings to their respective variables and execute
            $statement->execute($bind_vals);
            
            //after executing the query, save it (and the bound variables array) as the most recent query
            $_SESSION[last_query] = $sql;
            $_SESSION[last_vals] = $bind_vals;
            
            //if the result is at least a single row
            if ($statement->rowcount() > 0) {
                
                //save the results in the user_data variable and return
                $user_data = $statement->fetchall();
                return $user_data;
                
            //if there are no results
            } else {
                
                return FALSE;
            }
        }
    }
}

/**
* Function Name: 	set_password()
* Description:		Updates the password for a specific user in the database
* Parameters:		$user_id, an integer representation of the user's id
* Parameters:       $newPassword, a string representation of the new password to be updated
*/
function set_password($user_id, $new_password) {
    
    //set up the database object as global
    global $db;
    
    //set up the query to update the user's id
    $sql = "UPDATE users
            SET password = :new_password
            WHERE user_id = :user_id";
            
    //prepare the statement
    $statement = $db->prepare($sql);
    
    //bind our two values and execute the statement
    $statement->execute([
        
        ':new_password' => md5($new_password),
        ':user_id'      => $user_id,
        
    ]);
} 

/**
* Function Name: 	remove_user()
* Description:		Removes a user, and all of that user's data, from the database
* Parameters:		$user_id, an integer representation of the user's id
* Returns:          TRUE on successful user removal, or FALSE on user_id not found or attempted to remove an administrator
*/
function remove_user($user_id) {
    
    //make the database object global
    global $db;
    
    //set up the query to make sure the user exists
    $sql = "SELECT is_admin
            FROM users
            WHERE user_id = :user_id";
            
    //prepare the statement
    $statement = $db->prepare($sql);
    
    //execute and bind our variables
    $statement->execute([
        
        ':user_id' => $user_id,
        
    ]);
    
    //if the results do not contain a single row
    if ($statement->rowcount() < 1) {
        
        //report failure
        return FALSE;
        
    //otherwise, the user does indeed exist
    } else {
        
        //save the requested user's administrator status
        $admin_status = $statement->fetch()->is_admin;
        
        //if the requested user is an admin
        if ($admin_status == 1) {
            
            //report failure
            return FALSE;
        }
    }
    
    //now we know that the user exists and is not an admin, so we can begin removing the user's data from all of the user_ tables
    //first remove all potential user region data
    $sql = "DELETE FROM user_regions
            WHERE user_id = :user_id";
            
    $statement = $db->prepare($sql);
    
    $statement->execute([
        
        ':user_id' => $user_id,
        
    ]);
    
    //next remove all potential user instrument data
    $sql = "DELETE FROM user_instruments
            WHERE user_id = :user_id";
            
    $statement = $db->prepare($sql);
    
    $statement->execute([
        
        ':user_id' => $user_id,
        
    ]);
       
    //finally remove all potential user style data
    $sql = "DELETE FROM user_styles
            WHERE user_id = :user_id";
            
    $statement = $db->prepare($sql);
    
    $statement->execute([
        
        ':user_id' => $user_id,
        
    ]);
    
    //now that all of the user's data is removed from the database, we can remove the user themselves from the users table
    $sql = "DELETE FROM users
            WHERE user_id = :user_id";
            
    $statement = $db->prepare($sql);
    
    $statement->execute([
        
        ':user_id' => $user_id,
        
    ]);
    
    //return true to report success
    return TRUE;
}

