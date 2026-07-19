# Client Portal

> A secure digital workspace where clients can manage their relationship with Zytech Contractors.

---

# Vision

The Client Portal is not simply a login page.

It is a digital workspace where every client can:

- View quotations
- Download documents
- Communicate with Zytech
- Track projects
- Receive updates
- Approve documents
- Upload files
- Manage their profile

The experience should reduce unnecessary emails and phone calls while increasing transparency and customer confidence.

---

# Design Principles

The portal should feel:

Professional

The portal UI uses the same **handcrafted CSS** design system as the public website (`resources/css/portal`), sharing tokens from `resources/css/website/base/tokens.css`.

Do not style the portal with Tailwind. Filament Tailwind remains admin-only.

Minimal

Fast

Trustworthy

Modern

Responsive

Accessible

Premium

---

# Objectives

Provide transparency.

Improve communication.

Reduce manual work.

Increase customer trust.

Improve project visibility.

Centralize documents.

Support future online approvals.

Prepare for future online payments.

---

# Portal Architecture

Client Portal

│

├── Dashboard

├── My Projects

├── Quotations

├── Documents

├── Messages

├── Notifications

├── Profile

├── Security

└── Support

Authentication

Support

Email Login

Remember Me

Password Reset

Email Verification

Session Management

Future

Passkeys

Google Login

Microsoft Login

OTP

MFA

Client Dashboard

The dashboard should answer five questions immediately.

What projects do I have?

What quotations are waiting?

Did Zytech send me anything?

Do I need to do anything?

Who do I contact?
Dashboard Widgets

Welcome Card

Company Contact Card

Recent Notifications

Upcoming Meetings

Recent Documents

Pending Quotations

Project Progress

Unread Messages

Latest Knowledge Articles

Support Contacts

Dashboard Layout
------------------------------------------------

Top Navigation

------------------------------------------------

Welcome

↓

Project Summary

↓

Pending Quotations

↓

Recent Documents

↓

Messages

↓

Notifications

↓

Knowledge Centre

↓

Quick Actions

------------------------------------------------
Quick Actions

Request Quotation

Download Documents

Send Message

Upload Files

View Project

Book Meeting (Future)

Request Site Visit

Contact Manager

Navigation

Dashboard

Projects

Quotations

Documents

Messages

Knowledge Centre

Notifications

Profile

Security

Support

Logout

Portal Theme

Use the same design language as the public website.

Shared

Typography

Spacing

Buttons

Colors

Cards

Icons

Animations

This creates a seamless transition from marketing website to portal.

Portal Performance

Page loads under:

300ms cached

1 second uncached

Images optimized

Redis cached

Lazy loaded

Infinite scrolling where appropriate

Project Workspace

This is the client's view of a project.

Overview

Project Status

Timeline

Assigned Manager

Completion Percentage

Estimated Completion

Last Update

Timeline

Planning

↓

Approvals

↓

Construction

↓

Inspection

↓

Completion

Clients can view milestones but cannot edit them.

Gallery

Progress Images

Drone Images

Videos

360 Images (Future)

Before & After

Documents

Contracts

Invoices (Future)

Certificates

Plans

Approvals

Completion Documents

Clients may download only documents they are authorized to access.

Progress Updates

Every project update appears in chronological order.

Example

Foundation Complete

↓

Roof Installed

↓

Electrical Complete

↓

Painting Started

↓

Inspection Passed
Quotations

Clients can:

View

Download PDF

Accept

Reject

Request Changes

View Validity

Track Status

Every quotation should show a complete status timeline.

Messaging

Support conversations between:

Client

↓

Project Manager

↓

Sales

↓

Administration

Features

Rich text

Attachments

Read receipts

Typing indicator (Future)

Realtime updates using Laravel Reverb.

Notifications

Support

Quotation Updates

Project Updates

Document Uploads

Messages

Maintenance Notices

Security Alerts

Notification Center should allow:

Read

Unread

Archive

Filter

Search

Document Center

Organize documents by:

Projects

Quotations

Contracts

Certificates

Invoices (Future)

General

Support

Search

Preview

Download

Version History

File Uploads

Clients may upload:

Images

PDF

DOCX

ZIP

Blueprints

Videos

Uploads should support:

Virus scanning

Progress bars

Queued processing

Drag & Drop

Profile

Clients may update:

Phone

Address

Communication Preferences

Language

Profile Picture

Password

Security

View

Active Sessions

Recent Logins

Trusted Devices

Change Password

Enable MFA

Download Recovery Codes (Future)

Knowledge Centre

Clients can access

Educational Articles

Construction Guides

Planning Guides

Maintenance Guides

Relevant articles should automatically relate to:

Projects

Services

Quotations

Search

Global search across

Projects

Documents

Messages

Quotations

Knowledge Articles

Notifications

Redis Strategy

Cache

Dashboard

Notifications

Projects

Documents

Knowledge Articles

Invalidate selectively.

Laravel Reverb

Realtime updates

Messages

Notifications

Quotation Changes

Project Updates

Document Uploads

Queues

Generate PDFs

Email Notifications

Image Processing

Document Processing

Statistics

Large Uploads

Permissions

Every client may access only:

Their Projects

Their Quotations

Their Messages

Their Documents

Their Notifications

Use Policies everywhere.

Activity Logging

Record

Downloads

Logins

Profile Updates

Document Uploads

Quotation Acceptance

Messages

Security Changes

Mobile Experience

Mobile-first.

Large touch targets.

Bottom navigation.

Optimized uploads.

Offline-friendly where practical.

Accessibility

WCAG AA

Keyboard navigation

Screen readers

Focus states

High contrast

Reduced motion

Future Improvements

Digital Signatures

Online Payments

Meeting Scheduler

Live Video Consultation

Project Livestream

Client Mobile App

Push Notifications

Offline Mode

AI Assistant

Voice Search

Project Cost Dashboard

Warranty Tracker

Maintenance Scheduler

