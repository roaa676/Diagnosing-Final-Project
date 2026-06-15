# TODO - Assessment send button issue

## Step 1: Investigate current code paths
- [x] Read `assessment.component.ts` and confirm submission flow: `nextQuestion()` -> `finishAssessment()` -> `submitAssessmentResult()`
- [x] Identify guard preventing POST: `if (!this.childId || !this.difficultyId) return;`
- [x] Confirm ids come from query/localStorage with `> 0` validation (0/null becomes invalid)

## Step 2: Debug & fix
- [ ] Add explicit console logs + visible error message when ids are invalid (instead of silent return)
- [ ] Ensure `onShowQuestions()` exists in TS (currently referenced in HTML but not found)
- [ ] Re-test: click submit/finish and verify Network request appears


