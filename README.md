# Web-Project
Making a web application with data from e-katanalotis

!Notes:

* POIs.geojson is required for the User Main Menu page, to get POIs data/coordinates and then load them onto the map. It's used for loading/updating the POIs in the
database when the respective button is clicked in Admin Main Menu. The data is then taken from the database to create the markers on the map.
* prices_test.txt and products_test.txt are required to the Admin Main Menu to load/update products/categories/subactegories data in the database when the respective button is pressed.
* For both of the above cases, the file to load is selected automatically based on name and path (it's assumed that the file is in the same file as the corresponding php code. If you wish to change file name/directory you will have to make the same change to the corresponding php code, else the files won't be able to be loaded.
  
