# HKDSE Subjects - Complete List

This document lists all the HKDSE (Hong Kong Diploma of Secondary Education) subjects that are available in the system.

## Core Subjects

1. **Chinese Language** - 中國語文
2. **English Language** - 英國語文
3. **Mathematics Core** - 數學必修部分
4. **Citizenship and Social Development** - 公民與社會發展

## Mathematics Extended Modules

5. **Mathematics M1** - 數學延伸部分單元一 (Calculus and Statistics)
6. **Mathematics M2** - 數學延伸部分單元二 (Algebra and Calculus)

## Science Subjects

7. **Biology** - 生物
8. **Chemistry** - 化學
9. **Physics** - 物理

## Humanities Subjects

10. **Chinese History** - 中國歷史
11. **History** - 歷史
12. **Geography** - 地理

## Business & Social Sciences

13. **Economics** - 經濟
14. **Business, Accounting and Financial Studies** - 企業、會計與財務概論

## Technology

15. **Information and Communication Technology** - 資訊及通訊科技

## Arts & Other Subjects

16. **Ethics and Religious Studies** - 倫理與宗教
17. **Chinese Literature** - 中國文學
18. **Visual Arts** - 視覺藝術

---

## Implementation Details

### In the Application

- All subjects are available as a **dropdown menu** in both:
  - **Score Records** page (`scores.php`)
  - **Mistake Records** page (`mistakes.php`)

- The subject list is defined in `script.js` and automatically populates dropdowns using JavaScript

- Users **cannot enter custom subjects** - they must choose from the official HKDSE subject list

- This ensures:
  - Data consistency across the database
  - Proper filtering and grouping by subject
  - Accurate statistics per subject
  - No spelling errors or variations

### Total Scores

- Users can still **set their own total/full scores** for each exam
- Different exams may have different maximum scores
- The system automatically calculates the percentage based on:
  ```
  Percentage = (Score / Max Score) × 100
  ```

### Subject Selection

When adding a score or mistake record:
1. Click the "Subject" dropdown
2. Select from the list of 18 HKDSE subjects
3. The subject is stored exactly as selected
4. All records for the same subject will be grouped together

### Example Usage

**Adding a Score:**
- Subject: `Mathematics Core` (selected from dropdown)
- Exam Name: `Mock Exam 2024` (user input)
- Score: `85` (user input)
- Max Score: `100` (user input)
- Result: 85% automatically calculated

**Adding a Mistake:**
- Subject: `Biology` (selected from dropdown)
- Topic: `Cell Structure` (user input)
- Question: [User describes the question]
- Status: Unresolved/Reviewing/Resolved

---

## Database Storage

All subject names are stored in the `scores` and `mistakes` tables as VARCHAR(100) fields with the exact text from the dropdown selection.

### Benefits

✅ Consistent subject naming
✅ Easy filtering and sorting
✅ Accurate statistics per subject
✅ Professional data organization
✅ Matches official HKDSE structure

---

## Notes

- The subject list matches the official HKDSE curriculum
- Subjects are presented in a logical order (Core → Extended → Categories)
- English names are used in the application
- The dropdown is required - users must select a subject
- JavaScript automatically populates the dropdown on page load
- When editing records, the current subject is automatically selected

---

Last updated: 2026-01-12
