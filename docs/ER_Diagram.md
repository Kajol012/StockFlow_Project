# ER Diagram
```mermaid
erDiagram
 USERS ||--o{ SALES : creates
 USERS ||--o{ PURCHASES : creates
 USERS ||--o{ STOCK_TRANSACTIONS : records
 CATEGORIES ||--o{ PRODUCTS : contains
 PRODUCTS ||--o{ SALES : sold
 PRODUCTS ||--o{ PURCHASES : purchased
 PRODUCTS ||--o{ STOCK_TRANSACTIONS : changes
 CUSTOMERS ||--o{ SALES : makes
 SUPPLIERS ||--o{ PURCHASES : supplies
```
