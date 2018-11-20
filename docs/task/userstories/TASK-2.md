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
    - README.md (development guide) is updated
    - Calls to /appointment are properly handled
    - Tests present and pasing