# UserManagement API based on Symfony Standard Edition 


*Please see the diagram that shows the DB structure in the root of the project. table_diagram.png *
*Use the DB/SQL-usermanagement.sql dump for initialisation of the database.*
*Please note that some dev. implementation is incomplete. see in the end*

**Add Group**:

    *Endpoint url*: [env]/groups
    
    *Method*: [POST]
    
    *Headers*: ["Content-Type":"application/json"]
    
    *Json Parameters*: 
              Required: groupname (String)
              Required: permissionid (int)
    
    *Data Params* :  
            Example:  {
                        "groupname":"(string)",
                        "permissionid": "(int)"
                        }
            Example:  {
                        "groupname":"Administrator",
                        "permissionid": "1"
                        }
                        
    *Response Data Header*:  ["Content-Type":"application/json"]
    
    Response Data: 201 - successfully added
    Response Data: 200 - user already existed
    Response Data: 400 - missing paramters in request
    
    *Actual Request*:
    url: http://localhost/groups
    method: post
    request payload: {"groupname":"Administrator","permissionid": "1"}
    
    *Actual Response*:
    201 - successfully added

*Note*: I can add group 

------------------------------------
**Delete Group:**


    *Endpoint url*: [env]/groups/{groupname}
    
    *Method*: [DELETE]
    
    *Headers*: ["Content-Type":"application/json"]
    
    *Parameters*: 
    		  Required: groupname (String) [A-Za-z0-9_]+
    
    *Json Parameters*: 
             [NONE]
    
    *Data Params* :  
            [NONE]
           
                        
    *Response Data Header*:  ["Content-Type":"application/json"]
    
    Response Data: 204 - successfully deleted
    Response Data: 200 - group doesn't existed
    Response Data: 200 - Could not complete operation user(s) exits in this group
    Response Data: 400 - missing paramters in request
    
    *Actual Request*:
    url: http://localhost/groups/Administrator
    method: post
    request payload: [NONE]
    
    *Actual Response*:
    204 - successfully deleted

*Note*: I can delete groups when they no longer have members 

------------------------------------
**Remove single user 'connection' from a group:**

    *Endpoint url*: [env]/groups
    
    *Method*: [DELETE]
    
    *Headers*: ["Content-Type":"application/json"]
    *Json Parameters*: 
              Required: groupname (String)
              Required: username (String)
    
    *Data Params* :  
            Example:  {
                        "groupname":"(string)",
                        "username": "(string)"
                        }
            Example:  {
                        "groupname":"Administrators",
                        "username": "Tom"
                        }
                        
    *Response Data Header*:  ["Content-Type":"application/json"]
    
    Response Data: 204 - user is deleted from the group successfully
    Response Data: 400 - missing paramters in request
    
    *Actual Request*:
    url: http://localhost/groups
    method: DELETE
    request payload: {"groupname":"Administrators","username": "Tom"}
    
    *Actual Response*:
    204 - user is deleted from the group successfully
    200 - no user found to delete from the group

*Note*: I can delete single user from  a groups

-----------------------------------------------
**Remove all users 'connection' from a group:**

    *Endpoint url*: [env]/groups/users/{groupname}
    
    *Method*: [DELETE]
    
    *Headers*: ["Content-Type":"application/json"]
    
    *Parameters*: 
    		  Required: groupname (String) [A-Za-z0-9_]+
    
    *Json Parameters*: 
             [NONE]
    
    *Data Params* :  
            [NONE]
                
    *Response Data Header*:  ["Content-Type":"application/json"]
    
    Response Data: 204 - All users are deleted from the group successfully
    Response Data: 400 - missing paramters in request
    
    *Actual Request*:
    url: http://localhost/groups/users/Administrators
    method: DELETE
    request payload: [NONE]
    
    *Actual Response*:
    204 - All users are deleted from the group successfully

*Note*: I can delete all users from a group


-----------------------------------------------

**Add user to a group:**

    *Endpoint url*: [env]/user
    
    *Method*: [POST]
    
    *Headers*: ["Content-Type":"application/json"]
    
    **Json Parameters*: 
              Required: groupname (String)
    		       Required: username (String)
    
    
    *Data Params* :  
            Example:  {
                        "groupname":"(string)",
    					"username" :"(string)"
                        }
            Example:  {
    					"groupname":"Administrators"
                        "username":"Tom",
                        
                        }
          
                
    *Response Data Header*:  ["Content-Type":"application/json"]
    
    Response Data: 201 - successfully added
    Response Data: 200 - user already existed
    Response Data: 400 - missing paramters in request
    
    *Actual Request*:
    url: http://localhost/user
    method: post
    request payload: {"username":"Tom"}
    
    *Actual Response*:
    201 - successfully added

*Note*: I can add a user

-----------------------------------------------
**Assign user to a group if they are not existed:**

    *Endpoint url*: [env]/user
    
    *Method*: [PUT]
    
    *Headers*: ["Content-Type":"application/json"]
    
    **Json Parameters*: 
              Required: groupname (String)
    		       Required: username (String)
    
    
    *Data Params* :  
            Example:  {
                        "groupname":"(string)",
    					"username" :"(string)"
                        }
            Example:  {
    					"groupname":"Administrators",
                        "username":"Tom"
                        
                        }
          
                
    *Response Data Header*:  ["Content-Type":"application/json"]
    
    Response Data: 201 - successfully added
    Response Data: 200 - group doesn\'t existed
    Response Data: 200 - user is already in the group
    Response Data: 201 - user is apart of the group successfully
    
    *Actual Request*:
    url: http://localhost/user
    method: PUT
    request payload: {"groupname":"Administrators","username":"Tom"}
    
    *Actual Response*:
    201 - user is apart of the group successfully

*Note*: I can assign users to a group they aren’t already part of

-----------------------------------------------
**Delete User:**


    *Endpoint url*: [env]/user/{username}
    
    *Method*: [DELETE]
    
    *Headers*: ["Content-Type":"application/json"]
    
    *Parameters*: 
    		  Required: username (String) [A-Za-z0-9_]+
    
    *Json Parameters*: 
             [NONE]
    
    *Data Params* :  
            [NONE]
           
                        
    *Response Data Header*:  ["Content-Type":"application/json"]
    
    Response Data: 204 - successfully deleted
    Response Data: 404 - user is not found
    
    *Actual Request*:
    url: http://localhost/user/Tom
    method: delete
    request payload: [NONE]
    
    *Actual Response*:
    204 - successfully deleted

*Note*: I can delete user

==========================================
What was inclomplete: 
The permissions functional is not implemeneted.

@todo :
 - Validation of input paramters
 - Unit test
 - Functional tests
 - Code optimtzation
 
 





