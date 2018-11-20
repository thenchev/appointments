As **api user**

I want to:

    - I want to be able to add `owner` of appointment
    - I want to be able to add `participants` for appointment
    
As **developer**

I want to:

    - I want to be able to set relation between `owner` property in Appointment entity and Member entity
    - I want to be able to add `participants` for Appointment entity
    
    
**Acceptance criteria**:

    - Relations are properly set in doctrine
    - Database migrations are present
    - Calls to /appointment are properly handled
    - Functional tests are present and pasing
    - Unit tests are present and pasing