import React, { useRef } from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol, Input, Tag } from 'tdesign-react/esm'
import {
  PageTable,
  FilterItem,
  MediaText,
  CascaderAsync,
  TableRef,
  EditLink,
  DeleteLink,
  DeleteManyButton,
  CreateButton,
} from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      { colKey: 'row-select', type: 'multiple' },
      {
        colKey: 'id',
        sorter: true,
        sortType: 'all',
        title: 'ID',
        width: 100,
      },
      {
        colKey: 'title',
        title: translate('content.article.fields.title'),
        minWidth: 200,
        cell: ({ row }) => {
          return (
            <MediaText size='small'>
              {row.images?.[0] && <MediaText.Image src={row.images[0]}></MediaText.Image>}
              <MediaText.Title>{row.title}</MediaText.Title>
              <MediaText.Desc>{row.subtitle}</MediaText.Desc>
            </MediaText>
          )
        },
      },
      {
        colKey: 'class_name',
        title: translate('content.article.fields.category'),
        width: 200,
        cell: ({ row }) => {
          return row.class_name?.join(' > ')
        },
      },
      {
        colKey: 'source',
        title: translate('content.article.fields.source'),
        width: 200,
      },
      {
        colKey: 'status',
        title: translate('content.article.fields.status'),
        width: 150,
        filter: {
          type: 'single',
          list: [
            { label: translate('content.article.tab.published'), value: '1' },
            { label: translate('content.article.tab.unpublished'), value: '2' },
          ],
        },
        cell: ({ row }) => {
          return (
            <>
              {row.status ? (
                <Tag theme='warning' variant='outline'>
                  {translate('content.article.tab.published')}
                </Tag>
              ) : (
                <Tag theme='success' variant='outline'>
                  {translate('content.article.tab.unpublished')}
                </Tag>
              )}
            </>
          )
        },
      },
      {
        colKey: 'created_at',
        title: translate('content.article.fields.createdAt'),
        sorter: true,
        sortType: 'all',
        width: 200,
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 120,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <EditLink rowId={row.id} />
              <DeleteLink rowId={row.id} />
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  const table = useRef<TableRef>(null)

  return (
    <PageTable
      ref={table}
      columns={columns}
      tabs={[
        {
          label: translate('content.article.tab.all'),
          value: '0',
        },
        {
          label: translate('content.article.tab.published'),
          value: '1',
        },
        {
          label: translate('content.article.tab.unpublished'),
          value: '2',
        },
      ]}
      batchRender={() => (
        <>
          <DeleteManyButton
            rowIds={table.current?.selecteds || []}
            variant='outline'
            icon={<div className='t-icon i-tabler:trash'></div>}
          />
        </>
      )}
      actionRender={() => <CreateButton />}
      filterRender={() => {
        return (
          <>
            <FilterItem name='keyword'>
              <Input placeholder={translate('content.article.validate.title')} />
            </FilterItem>
            <FilterItem name='class_id'>
              <CascaderAsync
                placeholder={translate('content.article.validate.class')}
                url='content/category'
                keys={{
                  label: 'name',
                  value: 'id',
                }}
                format={(v) => parseInt(v)}
                filterable
                clearable
                checkStrictly
              />
            </FilterItem>
          </>
        )
      }}
    />
  )
}

export default List
